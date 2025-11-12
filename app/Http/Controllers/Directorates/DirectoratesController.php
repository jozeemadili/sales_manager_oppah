<?php

namespace App\Http\Controllers\Directorates;

use App\Http\Controllers\Controller;
use App\Models\Directorate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DirectoratesController extends Controller
{
    public function get()
    {
            $Directorate = Directorate::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
           return view('admin.hrms.directorates-registration',['Directorate' => $Directorate]);
    }
    public function register(Request $request)
    {
        // $request->validate(['first_name' => ['required', 'min:3'], 'email' => ['required', 'email','unique:users'],'password' => ['required','min:8','regex:/[a-z]/', 'regex:/[A-Z]/','regex:/[@$!%*#?&]/'], 'mobile' => ['required', 'min:9', 'max:9', 'unique:users']]);           
        $user = Directorate::create(
            [
                'dname'             => $request->dname,
                'htitle'            => $request->htitle,
                'address_details'   => $request->address_details,
                'regby'             => intval(Auth::user()->id),
                'company_id'        => intval(Auth::user()->company_id),
                'status'            => 'Active',
                'reg_date'          => date('Y-m-d H:i:s'),
            ]);
            return redirect()->route('directorates-management')->with('success', 'Directorate With name <b>'.strtoupper($request->dname).' </b> Successfully Registered  : ');
    }
    public function updateUserStatus(Request $request)
    {
        $User = Directorate::find($request->id);
        $User->status    = $request->status;
        $User->save();
        return to_route('directorates-management');
    }
}
