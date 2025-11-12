<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoresController extends Controller
{
    public function get()
    {
        if(Auth::user()->role == 'ADMIN')
        {
            $Branch = Store::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
        }else
        {
            $Branch = Store::where('company_id',Auth::user()->company_id)->where('id',Auth::user()->office_location)->orderBy('id','desc')->paginate(10);
        }
            
           return view('admin.sales_management.stores-registration',['Branch' => $Branch]);
    }
    public function register(Request $request)
    {   
        $user = Store::create(                
            [
                'name'                      => $request->name,
                'physica_addres'            => $request->physica_addres,
                'company_id'                => intval(Auth::user()->company_id),
                'status'                    => 'Active',
                'reg_at'                    => date('Y-m-d H:i:s'),
                'reg_by'                    => intval(Auth::user()->id),
                
            ]);
            return redirect()->route('stores-management')->with('success', 'Section With name <b>'.strtoupper($request->name).' </b> Successfully Registered  : ');
    }
    public function updateUserStatus(Request $request)
    {
        $User = Branch::find($request->id);
        $User->status    = $request->status;
        $User->save();
        return to_route('branches-management');
    }
}
