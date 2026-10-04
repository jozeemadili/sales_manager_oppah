<?php

namespace App\Http\Controllers\Stock;


use App\Models\User;
use App\Models\OurTruck;
use App\Models\TrucksDriver;
use App\Models\TrucksRoute;
use App\Models\Expense;
use App\Models\ExpensesRecordsTruck;
use App\Models\RoutePlan;
use App\Models\BankDeposist;
use App\Models\BankDepositFile;
use App\Models\TripLocation;



use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\TIRAClient\Scripts\Classes\Utils;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogisticsController extends Controller
{
    //
    public function get()
    {
            $User = User::where('status','Active')->where('role','Driver')->orderBy('id','desc')->get();
            $OurTruck = OurTruck::orderBy('id','desc')->paginate(10);
        
           return view('admin.sales_management.car-driver',['OurTruck' => $OurTruck,'User'=>$User]);
    }

    public function download($id) 
    {
        $TrucksRoute = TrucksRoute::find($id);
        if (!$TrucksRoute) {
            return response()->json(['responseCode' => '404', 'message' => 'Truck Route not found.']);
        }
    
        $ExpensesRecord = ExpensesRecordsTruck::where('route_id', $id)->orderBy('id', 'desc')->get();
        $RoutePlan = RoutePlan::where('route_id', $id)->orderBy('id', 'desc')->get();
    
        // Optional QR code generation
        $Utils = new Utils();  
        $qrcode = $Utils->getQrcode("https://oppah01.co.tz/l/{$id}", 50, 'svg');
        $svgContent = $Utils->svgToBase64($qrcode);

        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

       
        $trucksRoutes = TrucksRoute::with(['routePlans', 'expensesRecords'])
        ->where('truck_id', $TrucksRoute->truck_id)
       ->whereYear('route_date', $currentYear)
        ->whereMonth('route_date', $currentMonth)
        ->get();

            // Calculate totals
            $totalTransportFee = $trucksRoutes->sum('total_fee');
            $totalRouteFuel = $trucksRoutes->flatMap(fn($route) => $route->routePlans)->sum('amount_tsh');
            $totalExpenses = $trucksRoutes->flatMap(fn($route) => $route->expensesRecords)->sum('amount_used');

            $balanceRemainingMonth = $totalTransportFee - $totalRouteFuel - $totalExpenses;
    
        // Load PDF view with truck route data
        $pdf = Pdf::loadView('admin.sales_management.invoice-download-ledger', [
            'TrucksRoute' => $TrucksRoute,
            'ExpensesRecord' => $ExpensesRecord,
            'RoutePlan' => $RoutePlan,
            'qrcode' => $svgContent,
            'balanceRemainingMonth' => $balanceRemainingMonth
        ]);
        // Load PDF view with truck route data
        // $pdf = View('admin.sales_management.invoice-download-ledger', [
        //     'TrucksRoute' => $TrucksRoute,
        //     'ExpensesRecord' => $ExpensesRecord,
        //     'RoutePlan' => $RoutePlan,
        //     'qrcode' => $svgContent
        // ]);

        // return $pdf;

    
        return $pdf->download('truck-route-' . $TrucksRoute->id . '.pdf');
        // return View('truck-route-' . $TrucksRoute->id . '.pdf');

    }
    
     // return View('admin.sales_management.invoice-download', ['quotation' => $Invoice]);
            // return $pdf;
    public function getTriproutes()
    {
        if (Auth::user()->hasFullAccess()) {
            // Admin sees all trucks
            $OurTruck = OurTruck::orderBy('id', 'desc')->get();
        } 
        elseif (Auth::user()->role == 'Driver') {
            // Driver sees only their assigned truck(s)
            $OurTruck = OurTruck::where('driver_id', Auth::user()->id)
                                ->orderBy('id', 'desc')
                                ->get();
        } 
        else {
            // Optional: handle other roles (e.g., Manager, Mechanic)
            $OurTruck = collect(); // empty collection to avoid errors
        }
    
        $TrucksRoute = TrucksRoute::orderBy('id', 'desc')->paginate(10);
        $trip_no = self::generateTripNoAjax();
    
        return view('admin.sales_management.trip-routes', [
            'TrucksRoute' => $TrucksRoute,
            'OurTruck'    => $OurTruck,
            'trip_no'     => $trip_no
        ]);
    }

    public function getBankDeposit()
{
    $deposits = BankDeposist::with('files')->orderBy('id', 'desc')->paginate(10);

    $totalDeposits = BankDeposist::sum('deposited_amount');

    return view('admin.sales_management.bank-deposot-preview', [
        'deposits' => $deposits,
        'totalDeposits' => $totalDeposits
    ]);
}

    
    // public function storeBankDeposit()
    // {
    // }

    public function storeBankDeposit(Request $request)
    {
        $request->validate([
            'bank_name'        => 'required|string|max:255',
            'deposited_amount' => 'required|numeric|min:1',
            'deposit_origin'   => 'nullable|string|max:255',
            'file_path.*'      => 'nullable|file|mimes:jpg,jpeg,png,pdf',
        ]);
    
        // create deposit
        $deposit = BankDeposist::create([
            'bank_name'        => $request->bank_name,
            'deposited_amount' => $request->deposited_amount,
            'deposited_by'     => auth()->id(),
            'status'           => 'Pending',
            'deposited_date'   => $request->deposited_date,
            'deposit_origin'   => $request->deposit_origin,
            'created_at'       => now(),
        ]);
    
        // ✔ save multiple files
        if ($request->hasFile('file_path')) {
            foreach ($request->file('file_path') as $file) {
                // $path = $file->store('bank_deposits');
                $path = $file->store('bank_deposits', 'public');
    
                $deposit->files()->create([
                    'file_path' => $path,
                ]);
            }
        }
    
        return back()->with('success', 'Bank deposit added successfully!');
    }
    

    
    
    private function generateTripNo()
        {
            $currentMonth = strtoupper(Carbon::now()->format('M')); // e.g. NOV
            $prefix = 'OPPA';

            // Get the last trip this month
            $lastTrip = TrucksRoute::whereMonth('created_date', Carbon::now()->month)
                        ->whereYear('created_date', Carbon::now()->year)
                        ->orderBy('id', 'desc')
                        ->first();

            // Determine next sequence number
            if ($lastTrip && preg_match('/OPPA(\d{3})' . $currentMonth . '/', $lastTrip->trip_no, $matches)) {
                $lastNumber = (int)$matches[1];
                $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '001';
            }

            return $prefix . $nextNumber . $currentMonth; // e.g. OPPA001NOV
        }
    public function generateTripNoAjax()
    {
        $currentMonth = strtoupper(Carbon::now()->format('M')); // e.g. NOV
        $prefix = 'OPPA';

        // Find last trip for this month
        $lastTrip = TrucksRoute::whereMonth('created_date', Carbon::now()->month)
                    ->whereYear('created_date', Carbon::now()->year)
                    ->orderBy('id', 'desc')
                    ->first();

        if ($lastTrip && preg_match('/OPPA(\d{3})' . $currentMonth . '/', $lastTrip->trip_no, $matches)) {
            $lastNumber = (int)$matches[1];
            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '001';
        }

        $trip_no = $prefix . $nextNumber . $currentMonth;

        return response()->json(['trip_no' => $trip_no]);
    }

    public function saveTriproutes(Request $request)
    {
        // Validate input
        $request->validate([
            'route_date' => 'required|date',
            'going_customer' => 'required|string',
            'return_customer' => 'nullable|string',
            'going_transport_fee' => 'nullable|numeric',
            'return_transport_fee' => 'nullable|numeric',
            'total_fee' => 'nullable|numeric',
            'truck_id' => 'required|integer|exists:our_trucks,id',
        ]);
    
        // Auto-generate Trip No
        $trip_no = $this->generateTripNo();
        // Check for duplicates
            $duplicate = TrucksRoute::where('route_date', $request->route_date)
            ->where('going_customer', $request->going_customer)
            ->where('return_customer', $request->return_customer)
            ->where('going_transport_fee', $request->going_transport_fee)
            ->where('return_transport_fee', $request->return_transport_fee)
            ->where('truck_id', $request->truck_id)
            ->first();

        if ($duplicate) {
            return redirect()->back()->with('error', 'Duplicate entry detected. This truck route already exists!');
        }

    
        // Save new Truck Route (explicit style)
        $route = TrucksRoute::create([
            'route_date'             => $request->route_date,
            'trip_no'                => $trip_no,
            'going_customer'         => $request->going_customer,
            'return_customer'        => $request->return_customer,
            'going_transport_fee'    => $request->going_transport_fee,
            'return_transport_fee'   => $request->return_transport_fee,
            // 'total_fee'              => $request->total_fee,
            'created_by'             => Auth::user()->id,
            'created_date'           => Carbon::now(),
            'truck_id'               => $request->truck_id,
            'status'                 => 'Pending',
        ]);
    
        // return redirect()->back()->with('success', 'Truck route added successfully! Trip No: ' . $trip_no);
        TripLocation::fromRequest($request, $route->id, 'trip_created', $route->id);
        return redirect()->route('route-preview', ['id' => $route->id])->with('success', 'Truck route saved successfully!');
    }
    public function saveTruck(Request $request)
    {
        $request->validate([
            'plate_no' => 'required|string|max:255|unique:our_trucks,plate_no',
            'driver_id' => 'required|exists:users,id',
        ]);

        // 1. Save the truck
        $truck = OurTruck::create([
            'plate_no' => $request->plate_no,
            'driver_id' => $request->driver_id, // main driver
            'created_by' => Auth::id(),         // who created the truck
        ]);

        // 2. Save truck-driver assignment in TrucksDriver
        TrucksDriver::create([
            'driver_id' => $request->driver_id,
            'car_id' => $truck->id,
            'status' => 'active',
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Truck registered successfully!');
    }
    
   
    public function getTriproutesReports(Request $request)
    {
        $truckId = $request->input('truck_id');
        $month = $request->input('month');

        // Determine trucks based on route
        if ($request->route()->getName() == 'truck-ejy') {
            $truckId = 3;
            $trucks = OurTruck::where('id', 3)->get();
        } elseif ($request->route()->getName() == 'truck-erw') {
            $truckId = 5;
            $trucks = OurTruck::where('id', 5)->get();
        } else {
            // Exclude trucks 3 and 5
            $trucks = OurTruck::whereNotIn('id', [3, 5])->get();
        }

        $query = TrucksRoute::with(['routePlans', 'expensesRecords']);

        if ($truckId) {
            // Filter by selected truck
            $query->where('truck_id', $truckId);
        } else {
            // On the general report, exclude routes for trucks 3 and 5
            $query->whereNotIn('truck_id', [3, 5]);
        }

        if ($month) {
            $query->whereYear('route_date', substr($month, 0, 4))
                  ->whereMonth('route_date', substr($month, 5, 2));
        }

        $TrucksRoutes = $query->get();

        $totalTransportFee = $TrucksRoutes->sum('total_fee');
        $totalRouteFuel = $TrucksRoutes->flatMap(function ($route) {
            return $route->routePlans;
        })->sum('amount_tsh');

        $totalExpenses = $TrucksRoutes->flatMap(function ($route) {
            return $route->expensesRecords;
        })->sum('amount_used');

        $balanceRemaining = $totalTransportFee - $totalRouteFuel - $totalExpenses;

        return view('admin.sales_management.truck-reports', [
            'TrucksRoutes' => $TrucksRoutes,
            'totalTransportFee' => $totalTransportFee,
            'totalRouteFuel' => $totalRouteFuel,
            'totalExpenses' => $totalExpenses,
            'balanceRemaining' => $balanceRemaining,
            'trucks' => $trucks,
            'selectedTruck' => $truckId,
            'selectedMonth' => $month,
        ]);
    }

    

    public function RoutePreview($id) 
    {
        $TrucksRoute = TrucksRoute::find($id);
        $ExpensesRecord = ExpensesRecordsTruck::where('route_id',$id)->orderBy('id','desc')->get();
        $Expense = Expense::where('company_id',Auth::user()->company_id)->where('status','Active')->where('to_be_used','GARI')->orderBy('id','desc')->get();
        $RoutePlan = RoutePlan::where('route_id',$id)->orderBy('id','desc')->get();

        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

       
        $trucksRoutes = TrucksRoute::with(['routePlans', 'expensesRecords'])
        ->where('truck_id', $TrucksRoute->truck_id)
       ->whereYear('route_date', $currentYear)
        ->whereMonth('route_date', $currentMonth)
        ->get();

            // Calculate totals
            $totalTransportFee = $trucksRoutes->sum('total_fee');
            $totalRouteFuel = $trucksRoutes->flatMap(fn($route) => $route->routePlans)->sum('amount_tsh');
            $totalExpenses = $trucksRoutes->flatMap(fn($route) => $route->expensesRecords)->sum('amount_used');

            $balanceRemainingMonth = $totalTransportFee - $totalRouteFuel - $totalExpenses;
           
            
        return view('admin.sales_management.routes-preview',['TrucksRoute' => $TrucksRoute,'Expense'=>$Expense, 'ExpensesRecord'=>$ExpensesRecord,'RoutePlan'=>$RoutePlan,'balanceRemainingMonth'=>$balanceRemainingMonth]);
    }
    public static function getUserNameById($userId)
    {
        $user = User::find($userId);
        return $user ? $user->first_name : 'Unassigned';
    }
    public function registerNewExpensesTruck(Request $request)
    {   
		 // Check for duplicates based on expense_id and inventory_id
    $existingRecord = ExpensesRecordsTruck::where('expense_id', $request->expense_id)
        ->where('route_id', $request->inventory_id)
        ->first();

    if ($existingRecord) {
        return redirect()->back()->with('error', 'Duplicate expense record already exists for this inventory and expense type.');
    }
        $user = ExpensesRecordsTruck::create(   
            [
                'expense_id'          => $request->expense_id,
                'amount_used'          => $request->amount_used,
                'desr'                 => $request->desr,
                'company_id'            => intval(Auth::user()->company_id),
                'reg_by'            => intval(Auth::user()->id),
                'status'                => 'Active',
                'reg_at'                => date('Y-m-d H:i:s'),
                'route_id'          => $request->inventory_id,
                
            ]);
            TripLocation::fromRequest($request, $request->inventory_id, 'expense', $user->id);
            return redirect()->back()->with('success', 'Expenses Recorded  successful.');    

        }

        public function saveReoutePlan(Request $request)
        {  

            $request->validate([
                'inventory_id'   => 'required|exists:trucks_routes,id',
                'from_location'  => 'required|string|max:255',
                'to_location'    => 'required|string|max:255',
                'distance_km'    => 'required|numeric|min:0',
                'fuel_litres'    => 'required|numeric|min:0',
                'amount_tsh'     => 'required|numeric|min:0',
                'description'    => 'nullable|string',
            ]);
         // Check for duplicates
                $duplicate = RoutePlan::where('route_id', $request->inventory_id)
                ->where('from_location', $request->from_location)
                ->where('to_location', $request->to_location)
                ->where('distance_km', $request->distance_km)
                ->where('fuel_litres', $request->fuel_litres)
                ->where('amount_tsh', $request->amount_tsh)
                ->first();

            if ($duplicate) {
                return redirect()->back()->with('error', 'Duplicate entry detected. This route plan already exists!');
            }

            // Save new route plan
            $routePlan = RoutePlan::create([
                'route_id'       => $request->inventory_id,
                'from_location'  => $request->from_location,
                'to_location'    => $request->to_location,
                'distance_km'    => $request->distance_km,
                'fuel_litres'    => $request->fuel_litres,
                'amount_tsh'     => $request->amount_tsh,
                'description'    => $request->description,
                'created_by'     => intval(Auth::user()->id),
            ]);
        
            TripLocation::fromRequest($request, $request->inventory_id, 'route_plan', $routePlan->id);
            return redirect()->back()->with('success', 'Route Plan recorded successfully!');
        }

        public function deleteUnsubmittedRoute($id, $status)
        {
            // Delete related child records first
            RoutePlan::where('route_id', $id)->delete();
            ExpensesRecordsTruck::where('route_id', $id)->delete();

            // Now delete the parent
            TrucksRoute::find($id)?->delete();

            return redirect()->back()->with('success', 'All related Route data deleted successfully!');
        }
        public function deleteUnsubmittedDeposit($id, $status)
        {
            // Delete related child records first
            BankDepositFile::where('deposit_id', $id)->delete();
            // Now delete the parent
            BankDeposist::find($id)?->delete();

            return redirect()->back()->with('success', 'All related Route data deleted successfully!');
        }
        public function deleteUnsubmittedRoutePlan($id, $status)
        {
            
            RoutePlan::find($id)?->delete();
           
            return redirect()->back()->with('success', 'All related Route data deleted successfully!');
        }
        public function deleteUnsubmittedExpenseTrip($id, $status)
        {
            
            ExpensesRecordsTruck::find($id)?->delete();
           
            return redirect()->back()->with('success', 'All related Route data deleted successfully!');
        }

        public function sendProductsToApprove(Request $request)
        {
            DB::table('trucks_routes')
            ->where('id', $request->id)
            ->update(['status' => 'submitted']);
        return redirect()->back()->with('success', ' Submited  successful.');
        }

        public function sendProductsToApproveDeposit(Request $request)
        {
            DB::table('bank_deposists')
            ->where('id', $request->id)
            ->update(['status' => 'submitted']);
        return redirect()->back()->with('success', ' Submited  successful.');
        }



    // Route::post('/record-route-plan', [LogisticsController::class, 'saveReoutePlan'])->name('record-route-plan');

}
