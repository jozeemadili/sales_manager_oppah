<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\OurTruck;
use App\Models\TripLocation;
use App\Models\TrucksRoute;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Driver locations captured on trip actions, and the admin Trips Map.
 */
class TripLocationController extends Controller
{
    // Location sent just before the driver submits a trip (a GET link, so it
    // is posted separately by the browser and then the link is followed).
    public function store(Request $request, $id)
    {
        TrucksRoute::findOrFail($id);
        TripLocation::fromRequest($request, $id, 'submitted', $id);

        return response()->noContent();
    }

    public function map(Request $request)
    {
        abort_unless(Auth::user()->hasFullAccess(), 403);

        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : Carbon::today()->subDays(6);
        $to = $request->filled('to') ? Carbon::parse($request->to)->endOfDay() : Carbon::now()->endOfDay();
        $truckId = $request->input('truck_id');

        $locations = TripLocation::with(['route.our_truck', 'user'])
            ->whereRaw('COALESCE(recorded_at, created_at) BETWEEN ? AND ?', [$from, $to])
            ->when($truckId, fn ($q) => $q->whereHas('route', fn ($r) => $r->where('truck_id', $truckId)))
            ->orderByRaw('COALESCE(recorded_at, created_at)')
            ->get();

        $points = $locations->filter->hasPosition()->map(fn (TripLocation $l) => [
            'lat' => $l->latitude,
            'lng' => $l->longitude,
            'accuracy' => $l->accuracy_m,
            'truck' => optional(optional($l->route)->our_truck)->plate_no ?: 'Truck ?',
            'trip' => optional($l->route)->trip_no,
            'route_id' => $l->route_id,
            'event' => $l->eventLabel(),
            'tracking' => $l->event === 'tracking',
            'speed' => $l->speed_kmh,
            'by' => optional($l->user)->first_name,
            'time' => $l->takenAt()->format('d M Y H:i'),
        ])->values();

        // Last known position per truck ("last seen").
        $lastSeen = $points->groupBy('truck')->map(fn ($p) => $p->last())->values();

        return view('admin.sales_management.trips-map', [
            'points' => $points,
            'lastSeen' => $lastSeen,
            'noLocation' => $locations->reject->hasPosition()->values(),
            'trucks' => OurTruck::orderBy('plate_no')->get(['id', 'plate_no']),
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'truckId' => $truckId,
        ]);
    }
}
