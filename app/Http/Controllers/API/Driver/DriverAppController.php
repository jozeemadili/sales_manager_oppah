<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use App\Models\AppSyncRequest;
use App\Models\Expense;
use App\Models\ExpensesRecordsTruck;
use App\Models\OurTruck;
use App\Models\RoutePlan;
use App\Models\TripLocation;
use App\Models\TrucksRoute;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * API for the driver Android app (mobile/driver_app). Drivers log in with
 * their portal email + password and get a Sanctum token. Everything is
 * scoped to the driver's own truck(s), with the same rules as the portal
 * (LogisticsController). Entries carry the app's uuid so retries after a
 * dropped connection never create duplicates (AppSyncRequest).
 */
class DriverAppController extends Controller
{
    public function __construct()
    {
        // Every call except login needs a driver token from a still-active driver.
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            abort_unless($user && $user->tokenCan('driver') && $user->role === User::ROLE_DRIVER && $user->status === 'Active', 403, 'Driver access only.');

            return $next($request);
        })->except('login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => 'Wrong email or password. / Barua pepe au nenosiri si sahihi.']);
        }

        if ($user->role !== User::ROLE_DRIVER || $user->status !== 'Active') {
            throw ValidationException::withMessages(['email' => 'This app is for active drivers only. / Programu hii ni ya madereva tu.']);
        }

        $token = $user->createToken($data['device_name'] ?? 'driver-app', ['driver'])->plainTextToken;

        return response()->json(['token' => $token] + $this->profile($user));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['ok' => true]);
    }

    public function me(Request $request)
    {
        return response()->json($this->profile($request->user()));
    }

    public function trips(Request $request)
    {
        $trips = TrucksRoute::with(['our_truck'])
            ->whereIn('truck_id', $this->truckIds($request->user()))
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            ->map(fn ($t) => $this->tripSummary($t));

        return response()->json(['trips' => $trips]);
    }

    public function trip(Request $request, $id)
    {
        $trip = $this->ownTrip($request->user(), $id);

        return response()->json(['trip' => $this->tripDetail($trip)]);
    }

    public function createTrip(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'uuid' => ['nullable', 'string', 'max:64'],
            'route_date' => ['required', 'date'],
            'going_customer' => ['required', 'string', 'max:255'],
            'return_customer' => ['nullable', 'string', 'max:255'],
            'going_transport_fee' => ['nullable', 'numeric'],
            'return_transport_fee' => ['nullable', 'numeric'],
            'truck_id' => ['required', 'integer'],
        ]);

        if ($existing = AppSyncRequest::find_ref($user->id, $data['uuid'] ?? null, 'trip')) {
            return response()->json(['trip' => $this->tripDetail(TrucksRoute::findOrFail($existing)), 'duplicate' => true]);
        }

        abort_unless(in_array((int) $data['truck_id'], $this->truckIds($user), true), 403, 'Not your truck.');

        $duplicate = TrucksRoute::where('route_date', $data['route_date'])
            ->where('going_customer', $data['going_customer'])
            ->where('return_customer', $data['return_customer'] ?? null)
            ->where('going_transport_fee', $data['going_transport_fee'] ?? null)
            ->where('return_transport_fee', $data['return_transport_fee'] ?? null)
            ->where('truck_id', $data['truck_id'])
            ->first();

        if ($duplicate) {
            throw ValidationException::withMessages(['route_date' => 'This trip already exists. / Safari hii tayari ipo.']);
        }

        $trip = DB::transaction(function () use ($data, $user) {
            return TrucksRoute::create([
                'route_date' => $data['route_date'],
                'trip_no' => $this->nextTripNo(),
                'going_customer' => $data['going_customer'],
                'return_customer' => $data['return_customer'] ?? null,
                'going_transport_fee' => $data['going_transport_fee'] ?? null,
                'return_transport_fee' => $data['return_transport_fee'] ?? null,
                'created_by' => $user->id,
                'created_date' => Carbon::now(),
                'truck_id' => $data['truck_id'],
                'status' => 'Pending',
            ]);
        });

        AppSyncRequest::remember($user->id, $data['uuid'] ?? null, 'trip', $trip->id);
        $this->recordLocation($request, $trip->id, 'trip_created', $trip->id);

        return response()->json(['trip' => $this->tripDetail($trip)], 201);
    }

    public function addRoutePlan(Request $request, $id)
    {
        $user = $request->user();
        $trip = $this->ownTrip($user, $id);
        $data = $request->validate([
            'uuid' => ['nullable', 'string', 'max:64'],
            'from_location' => ['required', 'string', 'max:255'],
            'to_location' => ['required', 'string', 'max:255'],
            'distance_km' => ['required', 'numeric', 'min:0'],
            'fuel_litres' => ['required', 'numeric', 'min:0'],
            'amount_tsh' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        if (AppSyncRequest::find_ref($user->id, $data['uuid'] ?? null, 'route_plan')) {
            return response()->json(['trip' => $this->tripDetail($trip), 'duplicate' => true]);
        }

        $this->assertPending($trip);

        $duplicate = RoutePlan::where('route_id', $trip->id)
            ->where('from_location', $data['from_location'])
            ->where('to_location', $data['to_location'])
            ->where('distance_km', $data['distance_km'])
            ->where('fuel_litres', $data['fuel_litres'])
            ->where('amount_tsh', $data['amount_tsh'])
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages(['from_location' => 'This route plan already exists. / Mpango huu tayari upo.']);
        }

        $plan = RoutePlan::create([
            'route_id' => $trip->id,
            'from_location' => $data['from_location'],
            'to_location' => $data['to_location'],
            'distance_km' => $data['distance_km'],
            'fuel_litres' => $data['fuel_litres'],
            'amount_tsh' => $data['amount_tsh'],
            'description' => $data['description'] ?? null,
            'created_by' => $user->id,
        ]);

        AppSyncRequest::remember($user->id, $data['uuid'] ?? null, 'route_plan', $plan->id);
        $this->recordLocation($request, $trip->id, 'route_plan', $plan->id);

        return response()->json(['trip' => $this->tripDetail($trip->fresh())], 201);
    }

    public function addExpense(Request $request, $id)
    {
        $user = $request->user();
        $trip = $this->ownTrip($user, $id);
        $data = $request->validate([
            'uuid' => ['nullable', 'string', 'max:64'],
            'expense_id' => ['required', 'integer'],
            'amount_used' => ['required', 'numeric', 'min:1'],
            'desr' => ['nullable', 'string', 'max:500'],
        ]);

        if (AppSyncRequest::find_ref($user->id, $data['uuid'] ?? null, 'expense')) {
            return response()->json(['trip' => $this->tripDetail($trip), 'duplicate' => true]);
        }

        $this->assertPending($trip);
        abort_unless($this->expenseTypes()->contains('id', (int) $data['expense_id']), 422, 'Unknown expense type.');

        if (ExpensesRecordsTruck::where('expense_id', $data['expense_id'])->where('route_id', $trip->id)->exists()) {
            throw ValidationException::withMessages(['expense_id' => 'This expense type is already recorded for this trip. / Gharama hii tayari imerekodiwa.']);
        }

        $expense = ExpensesRecordsTruck::create([
            'expense_id' => $data['expense_id'],
            'amount_used' => $data['amount_used'],
            'desr' => $data['desr'] ?? null,
            'company_id' => (int) $user->company_id,
            'reg_by' => $user->id,
            'status' => 'Active',
            'reg_at' => Carbon::now(),
            'route_id' => $trip->id,
        ]);

        AppSyncRequest::remember($user->id, $data['uuid'] ?? null, 'expense', $expense->id);
        $this->recordLocation($request, $trip->id, 'expense', $expense->id);

        return response()->json(['trip' => $this->tripDetail($trip->fresh())], 201);
    }

    public function submitTrip(Request $request, $id)
    {
        $trip = $this->ownTrip($request->user(), $id);

        if ($trip->status === 'Pending') {
            $trip->update(['status' => 'submitted']);
            $this->recordLocation($request, $trip->id, 'submitted', $trip->id);
        }

        return response()->json(['trip' => $this->tripDetail($trip->fresh())]);
    }

    /**
     * Background tracking points, sent in batches (also those recorded
     * offline). Each point belongs to one of the driver's trips.
     */
    public function locations(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'points' => ['required', 'array', 'max:500'],
            'points.*.trip_id' => ['required', 'integer'],
            'points.*.lat' => ['required', 'numeric', 'between:-90,90'],
            'points.*.lng' => ['required', 'numeric', 'between:-180,180'],
            'points.*.accuracy' => ['nullable', 'numeric'],
            'points.*.speed' => ['nullable', 'numeric'],
            'points.*.recorded_at' => ['required', 'date'],
        ]);

        $ownTrips = TrucksRoute::whereIn('truck_id', $this->truckIds($user))->pluck('id')->all();
        $saved = 0;

        foreach ($data['points'] as $p) {
            if (!in_array((int) $p['trip_id'], $ownTrips, true) || ((float) $p['lat'] == 0 && (float) $p['lng'] == 0)) {
                continue;
            }

            $recordedAt = Carbon::parse($p['recorded_at'])->setTimezone(config('app.timezone'));

            // Skip exact repeats (a batch re-sent after a timeout).
            $exists = TripLocation::where('route_id', $p['trip_id'])->where('event', 'tracking')
                ->where('recorded_at', $recordedAt)->exists();
            if ($exists) {
                continue;
            }

            TripLocation::create([
                'route_id' => (int) $p['trip_id'],
                'event' => 'tracking',
                'latitude' => round((float) $p['lat'], 7),
                'longitude' => round((float) $p['lng'], 7),
                'accuracy_m' => isset($p['accuracy']) ? (int) round($p['accuracy']) : null,
                'speed_kmh' => isset($p['speed']) ? round((float) $p['speed'] * 3.6, 1) : null,
                'status' => 'ok',
                'recorded_by' => $user->id,
                'recorded_at' => $recordedAt,
                'source' => 'app',
            ]);
            $saved++;
        }

        return response()->json(['saved' => $saved]);
    }

    // ---------------------------------------------------------------------

    private function profile(User $user)
    {
        $trucks = OurTruck::whereIn('id', $this->truckIds($user))->get(['id', 'plate_no']);

        return [
            'user' => ['id' => $user->id, 'name' => trim($user->first_name.' '.$user->last_name), 'email' => $user->email],
            'trucks' => $trucks,
            'expense_types' => $this->expenseTypes()->values(),
        ];
    }

    private function truckIds(User $user)
    {
        return OurTruck::where('driver_id', $user->id)->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    private function expenseTypes()
    {
        return Expense::where('status', 'Active')->where('to_be_used', 'GARI')->orderBy('e_name')->get(['id', 'e_name as name']);
    }

    private function ownTrip(User $user, $id)
    {
        $trip = TrucksRoute::with('our_truck')->findOrFail($id);
        abort_unless(in_array((int) $trip->truck_id, $this->truckIds($user), true), 403, 'Not your trip.');

        return $trip;
    }

    private function assertPending(TrucksRoute $trip)
    {
        if ($trip->status !== 'Pending') {
            throw ValidationException::withMessages(['trip' => 'This trip was already submitted. / Safari hii imeshawasilishwa.']);
        }
    }

    // Same numbering as the portal (LogisticsController::generateTripNo), e.g. OPPA001OCT.
    private function nextTripNo()
    {
        $month = strtoupper(Carbon::now()->format('M'));
        $last = TrucksRoute::whereMonth('created_date', Carbon::now()->month)
            ->whereYear('created_date', Carbon::now()->year)
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $next = $last && preg_match('/OPPA(\d{3})'.$month.'/', $last->trip_no, $m)
            ? str_pad((int) $m[1] + 1, 3, '0', STR_PAD_LEFT)
            : '001';

        return 'OPPA'.$next.$month;
    }

    private function recordLocation(Request $request, $tripId, $event, $refId)
    {
        $request->merge(['geo_source' => 'app']);
        TripLocation::fromRequest($request, $tripId, $event, $refId);
    }

    private function tripSummary(TrucksRoute $t)
    {
        return [
            'id' => $t->id,
            'trip_no' => $t->trip_no,
            'route_date' => $t->route_date ? Carbon::parse($t->route_date)->toDateString() : null,
            'going_customer' => $t->going_customer,
            'return_customer' => $t->return_customer,
            'going_transport_fee' => (float) $t->going_transport_fee,
            'return_transport_fee' => (float) $t->return_transport_fee,
            'status' => $t->status,
            'truck' => optional($t->our_truck)->plate_no,
            'truck_id' => (int) $t->truck_id,
        ];
    }

    private function tripDetail(TrucksRoute $t)
    {
        $plans = RoutePlan::where('route_id', $t->id)->orderBy('id')->get();
        $expenses = ExpensesRecordsTruck::where('route_id', $t->id)->orderBy('id')->get();
        $types = $this->expenseTypes()->pluck('name', 'id');

        return $this->tripSummary($t) + [
            'route_plans' => $plans->map(fn ($p) => [
                'id' => $p->id, 'from_location' => $p->from_location, 'to_location' => $p->to_location,
                'distance_km' => (float) $p->distance_km, 'fuel_litres' => (float) $p->fuel_litres,
                'amount_tsh' => (float) $p->amount_tsh, 'description' => $p->description,
            ]),
            'expenses' => $expenses->map(fn ($e) => [
                'id' => $e->id, 'expense_id' => (int) $e->expense_id,
                'name' => $types[$e->expense_id] ?? optional(Expense::find($e->expense_id))->e_name,
                'amount_used' => (float) $e->amount_used, 'desr' => $e->desr,
            ]),
            'totals' => [
                'transport_fee' => (float) $t->going_transport_fee + (float) $t->return_transport_fee,
                'route_plans' => (float) $plans->sum('amount_tsh'),
                'expenses' => (float) $expenses->sum('amount_used'),
            ],
        ];
    }
}
