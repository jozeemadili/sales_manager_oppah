<?php

namespace App\Http\Controllers\Tuli;

use App\Http\Controllers\Controller;
use App\Models\CategoriesTuli;
use App\Models\ExpensesTuli;
use App\Models\InventoriesTuli;
use App\Models\StoresTuli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriesController extends Controller
{
    public function get()
    {
            $Branch = CategoriesTuli::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
           return view('admin.tuli_sales_management.categories-registration',['Branch' => $Branch]);
    }
    public function register(Request $request)
    {
        $user = CategoriesTuli::create(
            [
                'name'                      => $request->name,
                'descr'                      => $request->descr,
                'company_id'                => intval(Auth::user()->company_id),
                'status'                    => 'Active',
                'reg_at'                    => date('Y-m-d H:i:s'),
                'reg_by'                    => intval(Auth::user()->id),

            ]);
            return redirect()->route('categories-management-tuli')->with('success', 'Category With name <b>'.strtoupper($request->name).' </b> Successfully Registered  : ');
    }

    public function updateCategoryStatus(Request $request)
    {
        $User = CategoriesTuli::find($request->id);
        $User->status    = $request->status;
        $User->save();
        return to_route('categories-management-tuli');
    }

    public function getInventories()
    {
            $Branch = InventoriesTuli::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
            $companyNames = InventoriesTuli::where('company_id', Auth::user()->company_id)->distinct()->pluck('company_name');
            $companystore = StoresTuli::orderBy('id', 'desc')->get();

            // dd($companystore);


            return view('admin.tuli_sales_management.inventory-registration',['Branch' => $Branch,'companyNames'=>$companyNames,'companystore'=>$companystore]);
    }

    public function registerInvetories(Request $request)
    {
        $request->validate([
            'company_name'   => 'required|string|max:255',
            'description'    => 'required|string',
            'inventory_date' => 'required|date',
        ]);
        // Avoid duplicates: check if this exact entry already exists
        $exists = InventoriesTuli::where('company_id', Auth::user()->company_id)
            ->where('company_name', $request->company_name)
            ->where('inventory_date', $request->inventory_date)
            ->exists();

        if ($exists) {
            return redirect()
                ->route('invetories-management-tuli')
                ->with('warning', 'This inventory entry already exists and was not added again.');
        }

        // Create new inventory record
        InventoriesTuli::create([
            'company_name'    => $request->company_name,
            'vihecle_no'      => $request->vihecle_no,
            'description'     => $request->description,
            'inventory_date'  => $request->inventory_date,
            'company_id'      => intval(Auth::user()->company_id),
            'status'          => 'Active',
            'reg_at'          => now(),
            'reg_by'          => Auth::id(),
            'store_id'        => intval($request->store_id),
        ]);

        return redirect()
            ->route('invetories-management-tuli')
            ->with('success', 'Inventory from <b>' . strtoupper($request->company_name) . '</b> successfully registered.');
    }

    public function getmySuppliers(Request $request)
    {
        $query = InventoriesTuli::where('company_id', Auth::user()->company_id);

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

        return view('admin.tuli_sales_management.my-suppliers', ['Branch' => $branches]);
    }

    public function getExpensies()
    {
            $Branch = ExpensesTuli::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
           return view('admin.tuli_sales_management.expensies-registration',['Branch' => $Branch]);
    }

    public function registerExpenses(Request $request)
    {
        $user = ExpensesTuli::create(
            [
                'e_name'                      => $request->e_name,
                'company_id'                => intval(Auth::user()->company_id),
                'status'                    => 'Active',
                'reg_date'                    => date('Y-m-d H:i:s'),
                'reg_by'                    => intval(Auth::user()->id),

            ]);
            return redirect()->route('expenses-management-tuli')->with('success', 'expenses  <b>'.strtoupper($request->e_name).' </b> Successfully Registered  : ');
    }
}
