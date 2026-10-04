<?php

namespace App\Http\Controllers\Hrms;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Leaf;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeesController extends Controller
{
    public function get()
    {
            $users = Employee::orderBy('id','desc')->paginate(10);
           return view('admin.hrms.employees-registration',['users' => $users]);
    }
    public function leaveAdministration()
    {
            $users = Leaf::where('reg_by',Auth::user()->id)->orderBy('id','desc')->paginate(10);
           return view('admin.hrms.leave-registration',['users' => $users]);
    }
    public function profile($id)
    {
       
            $Employees= Employee::with('terms_of_contracts')->find($id);
            return view('admin.hrms.staff-profile',['Employees' => $Employees]);
       
    }
    public function registerLeave(Request $request)
    { 
        $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);
        
        $user = Leaf::create(
            [
                'start_date'     => $request->start_date,
                'end_date'       => $request->end_date,
                'leave_type'     => $request->leave_type,
                'phone_no'       => $request->contact_phone,
                'address'        => $request->address,
                'leave_details'        => $request->details,
                'reg_by'             => intval(Auth::user()->id),
                'emp_id'             => Auth::user()->emp_id,
                'status'            => 'Active',
                'reg_date'          => date('Y-m-d H:i:s')
                
            ]);
        //     return redirect()->route('leave-management')->with('success', 'Section With name <b>'.strtoupper($request->dname).' </b> Successfully Registered  : ');
            return redirect()->back()->with('success', 'Leave request submitted successfully.');
    }
}
