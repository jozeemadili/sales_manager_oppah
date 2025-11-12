<?php

namespace App\Http\Controllers\Stock;


use App\Models\User;
use App\Models\OurTruck;
use App\Models\TrucksDriver;
use App\Models\TrucksRoute;
use App\Models\Expense;
use App\Models\ExpensesRecordsTruck;
use App\Models\RoutePlan;





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

    public function getTriproutes()
    {
            // $User = User::where('status','Active')->where('role','Driver')->orderBy('id','desc')->get();
            $OurTruck = OurTruck::orderBy('id','desc')->get();
            $TrucksRoute = TrucksRoute::orderBy('id','desc')->paginate(10);
        $trip_no = self::generateTripNoAjax();
           return view('admin.sales_management.trip-routes',['TrucksRoute' => $TrucksRoute,'OurTruck'=>$OurTruck, 'trip_no' => $trip_no]);
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
        ]);
    
        return redirect()->back()->with('success', 'Truck route added successfully! Trip No: ' . $trip_no);
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
    public function RoutePreview($id) 
    {
        $TrucksRoute = TrucksRoute::find($id);
        $ExpensesRecord = ExpensesRecordsTruck::where('route_id',$id)->orderBy('id','desc')->get();
        $Expense = Expense::where('company_id',Auth::user()->company_id)->where('status','Active')->orderBy('id','desc')->get();
        $RoutePlan = RoutePlan::orderBy('id','desc')->get();
        return view('admin.sales_management.routes-preview',['TrucksRoute' => $TrucksRoute,'Expense'=>$Expense, 'ExpensesRecord'=>$ExpensesRecord,'RoutePlan'=>$RoutePlan]);
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
        
            return redirect()->back()->with('success', 'Route Plan recorded successfully!');
        }


    // Route::post('/record-route-plan', [LogisticsController::class, 'saveReoutePlan'])->name('record-route-plan');

}
