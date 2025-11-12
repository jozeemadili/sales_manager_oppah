<?php

namespace App\Http\Controllers\Directorates;

use App\Http\Controllers\Controller;
use App\Models\Directorate;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionsController extends Controller
{
    public function get()
    {
            $Directorate = Directorate::where('status','Active')->where('company_id',Auth::user()->company_id)->get();
            $Section = Section::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
           return view('admin.hrms.section-registration',['Section' => $Section,'Directorate'=>$Directorate]);
    }
    public function register(Request $request)
    {      
        $user = Section::create(
            [
                'sname'             => $request->dname,
                'htitle'            => $request->htitle,
                'address_details'   => $request->address_details,
                'directorate'       => $request->directorate,
                'regby'             => intval(Auth::user()->id),
                'company_id'        => intval(Auth::user()->company_id),
                'status'            => 'Active',
                'reg_date'          => date('Y-m-d H:i:s'),
            ]);
            return redirect()->route('section-management')->with('success', 'Section With name <b>'.strtoupper($request->dname).' </b> Successfully Registered  : ');
    }
    public function updateUserStatus(Request $request)
    {
        $User = Section::find($request->id);
        $User->status    = $request->status;
        $User->save();
        return to_route('section-management');
    }
}
