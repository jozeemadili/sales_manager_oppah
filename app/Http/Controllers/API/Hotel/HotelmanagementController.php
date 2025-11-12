<?php

namespace App\Http\Controllers\API\Hotel;

use App\Http\Controllers\Controller;
use App\Models\HotelCustomer;
use App\Models\HotelInvoice;
use App\Models\MyHotel;
use App\Models\Room;
use App\Models\RoomsCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HotelmanagementController extends Controller
{
    public function get()
    {
            $Directorate = MyHotel::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
           return view('admin.hotel.my-hotel',['Directorate' => $Directorate]);
           
    }
    public function register(Request $request)
    {
        // $request->validate(['first_name' => ['required', 'min:3'], 'email' => ['required', 'email','unique:users'],'password' => ['required','min:8','regex:/[a-z]/', 'regex:/[A-Z]/','regex:/[@$!%*#?&]/'], 'mobile' => ['required', 'min:9', 'max:9', 'unique:users']]);           
        $user = MyHotel::create(
            [
                'name'               => $request->hname,
                'physical_address'   => $request->address_details,
                'reg_by'             => intval(Auth::user()->id),
                'company_id'         => intval(Auth::user()->company_id),
                'status'             => 'Active',
                'reg_at'             => date('Y-m-d H:i:s'),
            ]);
            return redirect()->route('hotel-management')->with('success', 'Hotel With name <b>'.strtoupper($request->hname).' </b> Successfully Registered  : ');
    }
    public function getRoomCategories()
    {
            $Directorate = MyHotel::where('status','Active')->where('company_id',Auth::user()->company_id)->get();
            $Section = RoomsCategory::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
           return view('admin.hotel.room-category',['Section' => $Section,'Directorate'=>$Directorate]);

        //    return view('admin.forms.datepicker');

        //    resources/views/admin/forms/datepicker.blade.php
        //    resources/views/admin/hotel/room-category.blade.php
    }
    public function getRooms()
    {
            $Directorate = RoomsCategory::where('status','Active')->where('company_id',Auth::user()->company_id)->get();
            $Section = Room::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
           return view('admin.hotel.room-management',['Section' => $Section,'Directorate'=>$Directorate]);
        //    resources/views/admin/hotel/room-category.blade.php
    }
    public function registerRoomCategories(Request $request)
    {      
        $user = RoomsCategory::create(
            [
                'category_name'             => $request->dname,
                'ammenties'                 => $request->ammenties,
                'price_day'                 => $request->price_day,
                // 'hotel_id'                  => $request->hotel_id,
                'hotel_id'                  => 1,
                'currency'                  => $request->currency,
                'created_by'                => intval(Auth::user()->id),
                'company_id'                => intval(Auth::user()->company_id),
                'status'                    => 'Active',
                'created_at'                => date('Y-m-d H:i:s'),
            ]);
            return redirect()->route('room-category-management')->with('success', 'Category With name <b>'.strtoupper($request->dname).' </b> Successfully Registered  : ');
    }
    public function registerRoom(Request $request)
    {      
        $user = Room::create(
            [
                'room_name'                 => $request->room_name,
                'status'                    => 'Active',
                'occupied'                  => 'no',
                'created_by'                => intval(Auth::user()->id),
                'category_id'               => $request->category_id,
                'company_id'                => intval(Auth::user()->company_id),
                'created_at'                => date('Y-m-d H:i:s'),
            ]);
            return redirect()->route('room-management')->with('success', 'Room With name <b>'.strtoupper($request->room_name).' </b> Successfully Registered  : ');
    }
    public function getMyCustomers()
    {
            $Branch = HotelCustomer::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
           return view('admin.hotel.hotel-customer',['Branch' => $Branch]);
           
    }
    public function registerHotelCustomer(Request $request)
    {   
        $user = HotelCustomer::create(  
            [
                'name'                          => $request->name,
                'mobile'                        => $request->mobile,
                'email'                         => $request->email,
                'tribe'                         => $request->tribe,
                'occupation'                    => $request->occupation,
                'physical_addres'               => $request->physical_addres,
                'company_id'                    => intval(Auth::user()->company_id),
                'status'                        => 'Active',
                'created_at'                    => date('Y-m-d H:i:s'),
                'created_by'                    => intval(Auth::user()->id),
                
            ]);
            return redirect()->route('mycustomers-management')->with('success', 'Customer With name <b>'.strtoupper($request->name).' </b> Successfully Registered  : ');
    }
    public function HotelCustomerProfile($id)
    {
       
            $Customers= HotelCustomer::find($id);
            $HotelInvoice= HotelInvoice::where('customer_id',$id)->with('hotel_invoice_items')->get();
            // $sales = Sale::where('customer_id',$id)->paginate(10);
            // resources/views/admin/hotel/customer-profile-hotel.blade.php
            return view('admin.hotel.customer-profile-hotel',['Customers' => $Customers,'HotelInvoice'=>$HotelInvoice]);
       
    }
}
