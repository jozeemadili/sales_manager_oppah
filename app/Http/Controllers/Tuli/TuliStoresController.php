<?php

namespace App\Http\Controllers\Tuli;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\StoresTuli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TuliStoresController extends Controller
{
    public function get()
    {
        if(Auth::user()->role == 'ADMIN')
        {
            $Branch = StoresTuli::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
        }else
        {
            $Branch = StoresTuli::where('company_id',Auth::user()->company_id)->where('id',Auth::user()->office_location)->orderBy('id','desc')->paginate(10);
        }
            
           return view('admin.tuli_sales_management.stores-registration',['Branch' => $Branch]);
    }
    public function register(Request $request)
    {   
        $user = StoresTuli::create(                
            [
                'name'                      => $request->name,
                'physica_addres'            => $request->physica_addres,
                'company_id'                => intval(Auth::user()->company_id),
                'status'                    => 'Active',
                'reg_at'                    => date('Y-m-d H:i:s'),
                'reg_by'                    => intval(Auth::user()->id),
                
            ]);
            return redirect()->route('stores-management-tuli')->with('success', 'Section With name <b>'.strtoupper($request->name).' </b> Successfully Registered  : ');
    }
    public function updateUserStatus(Request $request)
    {
        $User = StoresTuli::find($request->id);
        $User->status    = $request->status;
        $User->save();
        return to_route('stores-management-tuli');
    }
}



