<?php

namespace App\Http\Controllers\Branches;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class BranchesController extends Controller
{
    public function get()
    {
            $Branch = Branch::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
           return view('admin.hrms.branch-registration',['Branch' => $Branch]);
    }
    public function register(Request $request)
    {   
        $user = Branch::create(
            [
                'branch_id'              => $request->branch_id,
                'branch_name'            => $request->branch_name,
                'company_id'             => intval(Auth::user()->company_id),
                'status'                 => 'Active',
            ]);
            return redirect()->route('branches-management')->with('success', 'Section With name <b>'.strtoupper($request->branch_name).' </b> Successfully Registered  : ');
    }
    public function updateUserStatus(Request $request)
    {
        $User = Branch::find($request->id);
        $User->status    = $request->status;
        $User->save();
        return to_route('branches-management');
    }
}
