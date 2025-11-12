<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;

class CategoriesController extends Controller
{
    public function get()
    {
            $Branch = Category::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
           return view('admin.sales_management.categories-registration',['Branch' => $Branch]);
    }
    public function getInventories()
    {
            $Branch = Inventory::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
            $companyNames = Inventory::where('company_id', Auth::user()->company_id)->distinct()->pluck('company_name');

           return view('admin.sales_management.inventory-registration',['Branch' => $Branch,'companyNames'=>$companyNames]);
    }
    // public function getmySuppliers()
    // {
    //     $branches = Inventory::where('company_id', Auth::user()->company_id)->select('company_name')->distinct()->orderBy('company_name', 'asc')->get();
    //     return view('admin.sales_management.my-suppliers',['Branch' => $branches]);
    // }
    public function getmySuppliers(Request $request)
    {
        $query = Inventory::where('company_id', Auth::user()->company_id);
    
        // Filter by company name if search exists
        if ($request->filled('company_name')) {
            $query->where('company_name', 'LIKE', '%' . $request->company_name . '%');
        }
    
        // Paginate results (10 per page)
        $branches = $query->select('company_name')
                          ->distinct()
                          ->orderBy('company_name', 'asc')
                          ->paginate(10);
    
        // Preserve query parameters for pagination links
        $branches->appends($request->all());
    
        return view('admin.sales_management.my-suppliers', ['Branch' => $branches]);
    }
    

    public function getExpensies()
    {
            $Branch = Expense::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
           return view('admin.sales_management.expensies-registration',['Branch' => $Branch]);
    }
    public function register(Request $request)
    {   
        $user = Category::create(  
            [
                'name'                      => $request->name,
                'descr'                      => $request->descr,
                'company_id'                => intval(Auth::user()->company_id),
                'status'                    => 'Active',
                'reg_at'                    => date('Y-m-d H:i:s'),
                'reg_by'                    => intval(Auth::user()->id),
                
            ]);
            return redirect()->route('categories-management')->with('success', 'Category With name <b>'.strtoupper($request->name).' </b> Successfully Registered  : ');
    }
    public function registerInvetories(Request $request)
{
    $request->validate([
        'company_name'   => 'required|string|max:255',
        'vihecle_no'     => ['required', 'string', 'max:50', 'regex:/^[A-Z0-9]+$/i'],
        'description'    => 'required|string',
        'inventory_date' => 'required|date',
    ]);
    // Avoid duplicates: check if this exact entry already exists
    $exists = Inventory::where('company_id', Auth::user()->company_id)
        ->where('company_name', $request->company_name)
        ->where('vihecle_no', $request->vihecle_no)
        ->where('inventory_date', $request->inventory_date)
        ->exists();

    if ($exists) {
        return redirect()
            ->route('invetories-management')
            ->with('warning', 'This inventory entry already exists and was not added again.');
    }

    // Create new inventory record
    Inventory::create([
        'company_name'    => $request->company_name,
        'vihecle_no'      => $request->vihecle_no,
        'description'     => $request->description,
        'inventory_date'  => $request->inventory_date,
        'company_id'      => intval(Auth::user()->company_id),
        'status'          => 'Active',
        'reg_at'          => now(),
        'reg_by'          => Auth::id(),
    ]);

    return redirect()
        ->route('invetories-management')
        ->with('success', 'Inventory from <b>' . strtoupper($request->company_name) . '</b> successfully registered.');
}

    public function registerExpenses(Request $request)
    {   
        $user = Expense::create(
            [
                'e_name'                      => $request->e_name,
                'company_id'                => intval(Auth::user()->company_id),
                'status'                    => 'Active',
                'reg_date'                    => date('Y-m-d H:i:s'),
                'reg_by'                    => intval(Auth::user()->id),
                
            ]);
            return redirect()->route('expenses-management')->with('success', 'expenses  <b>'.strtoupper($request->e_name).' </b> Successfully Registered  : ');
    }
    public function updateUserStatus(Request $request)
    {
        $User = Branch::find($request->id);
        $User->status    = $request->status;
        $User->save();
        return to_route('branches-management');
    }
}
