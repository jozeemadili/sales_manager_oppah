<?php

namespace App\Http\Controllers\Tuli;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoriesTuli;
use App\Models\InventoriesTuli;
use App\Models\ProductsTuli;
use App\Models\StoresTuli;
use App\Models\ExpensesRecordsTuli;
use App\Models\ExpensesTuli;
use App\Models\ProductsInventoriesTuli;
use App\Models\CustomersTuli;
use App\Models\SalesTuli;
use App\Models\InvoicesTuli;
use Carbon\Carbon;
use App\Models\InvoiceItemsTuli;
use App\Models\InvoicePaymentDetailsTuli;





use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

class inventoryManagentController extends Controller
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

    public function InventoryPreview($id) 
    {
        $barcodeValue = rand(1000000000, 9999999999);

        // $Products = Product::where('status','Active')->get();
        $Inventory = InventoriesTuli::find($id);
        $Products = ProductsTuli::where('status','Active')->where('store_id',$Inventory->store_id)->get();
        $Branch = ProductsInventoriesTuli::where('company_id',Auth::user()->company_id)->where('inventory_id',$id)->orderBy('id','desc')->paginate(10);
        $stores = StoresTuli::where('company_id',Auth::user()->company_id)->where('status','Active')->orderBy('id','desc')->get();
        $Categories = CategoriesTuli::where('company_id',Auth::user()->company_id)->where('status','Active')->orderBy('id','desc')->get();
        // $Expense = Expense::where('company_id',Auth::user()->company_id)->where('status','Active')->orderBy('id','desc')->get();
        $Expense = ExpensesTuli::where('company_id',Auth::user()->company_id)->where('status','Active')->where('to_be_used','duka')->orderBy('id','desc')->get();
        $ExpensesRecord = ExpensesRecordsTuli::where('company_id',Auth::user()->company_id)->where('inventory_id',$id)->orderBy('id','desc')->get();

        $AllProducts = ProductsInventoriesTuli::where('company_id',Auth::user()->company_id)->where('inventory_id',$id)->orderBy('id','desc')->get();
        // dd($ExpensesRecord);
           
   
        return view('admin.tuli_sales_management.inventory-preview',['Products' => $Products,'Inventory'=>$Inventory,'Branch'=>$Branch,'stores'=>$stores,'Categories'=>$Categories,'barcodeValue'=>$barcodeValue,'Expense'=>$Expense,'ExpensesRecord'=>$ExpensesRecord,'AllProducts'=>$AllProducts]);
    }
    public function registerNewExpenses(Request $request)
    {   
		 // Check for duplicates based on expense_id and inventory_id
    $existingRecord = ExpensesRecordsTuli::where('expense_id', $request->expense_id)
        ->where('inventory_id', $request->inventory_id)
        ->first();

    if ($existingRecord) {
        return redirect()->back()->with('error', 'Duplicate expense record already exists for this inventory and expense type.');
    }
        $user = ExpensesRecordsTuli::create(   
            [
                'expense_id'          => $request->expense_id,
                'amount_used'          => $request->amount_used,
                'desr'                 => $request->desr,
                'company_id'            => intval(Auth::user()->company_id),
                'reg_by'            => intval(Auth::user()->id),
                'status'                => 'Active',
                'reg_at'                => date('Y-m-d H:i:s'),
                'inventory_id'          => $request->inventory_id,
                
            ]);
            return redirect()->back()->with('success', 'Expenses Recorded  successful.');    

    }
    public function registerInventory(Request $request)
    {   
		// 'expire_date',
        $request->validate([
            // 'product_name' => 'required|string|max:255',
            'qty' => 'required|integer',
            'category' => 'required|string|max:255',
            'barcode' => 'required|string|max:100|unique:products,barcode',
            'purchasing_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);
        // Check for duplicate
        $existingProduct = ProductsInventoriesTuli::where('product_name', $request->product_name)
            ->where('inventory_id', $request->inventory_id)
            ->first();

        if ($existingProduct) {
            return redirect()->back()->with('error', 'Product with name <b>' . strtoupper($request->product_name) . '</b> already exists in this inventory.');
        }
            
        $inventory = InventoriesTuli::findOrFail($request->inventory_id);

            $user = ProductsInventoriesTuli::create(   
                [
                    'product_name'          => $request->product_name,
                    'qty'                   => $request->qty,
                    'qty_remained'          => $request->qty,
                    'unit_of_measuer'       => $request->unit_of_measuer,
                    'category'              => $request->category,
                    'barcode'               => $request->barcode,
                    'purchasing_price'      => $request->purchasing_price,
                    'selling_price'         => $request->selling_price,
                    'description'           => $request->description,
                    'store_id'              => $inventory->store_id,
                    'company_id'            => intval(Auth::user()->company_id),
                    'created_by'            => intval(Auth::user()->id),
                    'status'                => 'Waiting',
                    'reg_at'                => date('Y-m-d H:i:s'),
                    'inventory_id'          => $request->inventory_id,
                ]);
        return redirect()->back()->with('success', 'Product With name <b>'.strtoupper($request->product_name).' </b> Successfully Registered  : ');
    }

    public function sendProductsTostock(Request $request)
    {
        // dd("id" .$request->id);
        $inventoryId = $request->id;
    
        $inventory = DB::table('inventories_tuli')->where('id', $inventoryId)->first();
    
        // dd($inventory->status);
        // If already submitted, prevent reprocessing
        if (!$inventory || $inventory->status === 'submitted') {
            return redirect()->back()->with('warning', 'Stock already submitted.');
        }
    
        DB::transaction(function () use ($inventoryId, $inventory) {
            $records = DB::table('products_inventories_tuli')
                ->where('inventory_id', $inventoryId)
                ->get();
    
            foreach ($records as $item) {
                // Convert to array
                $itemArray = (array) $item;
    
                // Try to find existing product
                $existing = DB::table('products_tuli')
                    ->where('product_name', $item->product_name)
                    ->where('store_id', $inventory->store_id)
                    ->first();
    
                if ($existing) {
                    DB::table('products_tuli')
                        ->where('product_name', $item->product_name)
                        ->where('store_id', $inventory->store_id)
                        ->update([
                            'qty' => $existing->qty + $item->qty,
                            'qty_remained' => $existing->qty_remained + $item->qty,
                            'purchasing_price' => $item->purchasing_price,
                            'selling_price' => $item->selling_price,
                            'store_id' => $inventory->store_id, // ✅ added
                            'status' => 'Active',
                        ]);
                } else {
                    // Insert new product
                    $itemArray['qty_remained'] = $item->qty;
                    $itemArray['store_id'] = $inventory->store_id; // ✅ added
                    $itemArray['status'] = 'Active'; // ✅ ADD THIS LINE
    
                    DB::table('products_tuli')->insert($itemArray);
                }
            }
    
            DB::table('products_inventories_tuli')
                ->where('inventory_id', $inventoryId)
                ->update(['status' => 'approved']);
    
            DB::table('inventories_tuli')
                ->where('id', $inventoryId)
                ->update(['status' => 'submitted']);
        });
    
        return redirect()->back()->with('success', 'Stock Submited successful.');
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

    public function getAllProducts()
    {
        $barcodeValue = rand(1000000000, 9999999999);

      
            $stores = StoresTuli::where('company_id',Auth::user()->company_id)->where('status','Active')->orderBy('id','desc')->get();
            $Categories = CategoriesTuli::where('company_id',Auth::user()->company_id)->where('status','Active')->orderBy('id','desc')->get();
               if(Auth::user()->role == 'ADMIN')
                {
                    $Branch = ProductsTuli::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
        
                }
                else{
                    $Branch = ProductsTuli::where('company_id',Auth::user()->company_id)->where('store_id',Auth::user()->office_location)->orderBy('id','desc')->paginate(10);
        
               }
           return view('admin.tuli_sales_management.products-registration',['Branch' => $Branch,'Categories'=>$Categories,'stores'=>$stores,'barcodeValue'=>$barcodeValue]);
    }

    public function getAllCustomers()
    {
        // if(Auth::user()->email == 'jchaboma@oppah01.co.tz')
        // {
            $Branch = CustomersTuli::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
            
           return view('admin.tuli_sales_management.customers-registration',['Branch' => $Branch]);
    }

    public function registerCustomer(Request $request)
    {   
        $existingCustomer = CustomersTuli::where('name', $request->name)->first();

        if ($existingCustomer) {
            return redirect()->route('customers-management')->with('success', 'Customer with this name already exists <b>'.strtoupper($request->name).' </b> Seems You submitted more than once Details');
        }
      
        // Proceed to create new customer
        $user = CustomersTuli::create([
            'name'            => $request->name,
            'tin'             => $request->tin,
            'vrn'             => $request->vrn,
            'phone'           => $request->phone,
            'email'           => $request->email,
            'physical_addres' => $request->physical_addres,
            'company_id'      => intval(Auth::user()->company_id),
            'status'          => 'Active',
            'created_at'      => now(),
            'created_by'      => intval(Auth::user()->id),
            'store_id'      => 1,
            
        ]);
        return redirect()->route('customers-management')->with('success', 'Customer With name <b>'.strtoupper($request->name).' </b> Successfully Registered  : ');
    }
    public function profile($id)
    {
        $customer = CustomersTuli::findOrFail($id);
    
        $invoices = InvoicesTuli::where('customer_id', $id)
            ->with('invoice_items_tuli', 'invoice_payment_details_tuli', 'customers_tuli', 'User')
            ->get();
    
        $sales = SalesTuli::where('customer_id', $id)->paginate(10);
    
        // ===== Grand Totals =====
        $grandTotalQty = 0;
        $grandTotalAmount = 0;
        $grandTotalPaid = 0;
        $grandTotalRemained = 0;
    
        foreach ($invoices as $invoice) {
            $invoiceQty = $invoice->invoice_items_tuli->sum('qty');
    
            $grandTotalQty += $invoiceQty;
            $grandTotalAmount += $invoice->total_invoice_amount;
            $grandTotalPaid += $invoice->amount_paid;
            $grandTotalRemained += $invoice->amount_remained;
        }
    
        return view('admin.tuli_sales_management.customer-profile', [
            'Customers' => $customer,
            'Invoice' => $invoices,
            'sales' => $sales,
            'grandTotalQty' => $grandTotalQty,
            'grandTotalAmount' => $grandTotalAmount,
            'grandTotalPaid' => $grandTotalPaid,
            'grandTotalRemained' => $grandTotalRemained,
        ]);
    }

    public function invoicePreviewTuli($id) 
    {

        $Invoice = InvoicesTuli::with('invoice_items_tuli')->find($id);
        if($Invoice != null)
        {
            // $pdf       = Pdf::loadView('admin.sales_management.invoice-download', ['quotation' => $Invoice]);
            return View('admin.tuli_sales_management.invoice-preview', ['quotation' => $Invoice,'invoice_id'=>$id]);
            // return $pdf;
            // return $pdf->download(strtolower(str_replace(' ','',$Invoice->id)).'_'.$Invoice->id.'.pdf');
        }

        return array('responseCode' => '404', 'message' => 'Invoice not Found.');
    }

    public function invoiceStatusUpdate($id) 
    {
        $invoice = InvoicesTuli::find($id);
    
        // Check if invoice is already confirmed
        if ($invoice && $invoice->status !== "Confirmed") {
    
            // Update invoice status and date
            $invoice->status = "Confirmed"; 
            $invoice->invoice_date = Carbon::now();
            $invoice->save();

            // Update all sales linked to this invoice
            SalesTuli::where('invoice_issued_id', $id)
            ->update([
                'status' => 'invoiced_confirmed'
            ]);
    
            // Get all items for this invoice
            $invoiceItems = InvoiceItemsTuli::where('invoice_id', $id)->get();
    
            foreach ($invoiceItems as $item) {
                $product = ProductsTuli::find($item->product_id);
    
                if ($product) {
                    // Update product stock info
                    $product->invoiced_qty = $item->qty;
                    $product->qty_remained -= $item->qty;
                    $product->save();
                }
            }
    
            return redirect()->back()->with('success', 'Invoice marked as Confirmed successfully.');
    
        } else {
            return redirect()->back()->with('success', 'Duplicate request');
        }
    }

    public function invoiceStatusUpdatePaid(Request $request) 
    {
        // dd($request->item_id);
        $id=$request->item_id;
        $Invoice = InvoicesTuli::find($id);
    
        if (!$Invoice) {
            return redirect()->back()->with('error', 'Invoice not found.');
        }
    
        // Get the amount entered by the user
        $newAmount = floatval($request->input('discount_amount', 0));
    
        // Check if the amount exceeds remaining amount
        if ($newAmount > $Invoice->amount_remained) {
            return redirect()->back()->with('error', 'Entered amount exceeds remaining amount.');
        }
    
        // Update invoice totals
        $Invoice->amount_paid += $newAmount;
        $Invoice->amount_remained -= $newAmount;
    
        // Update invoice status based on remaining amount
        if ($Invoice->amount_remained <= 0) {
            $Invoice->status = 'Paid';
            $Invoice->amount_remained = 0; // avoid negative
            $Invoice->paid_by = auth()->user()->first_name;
            $Invoice->date_paid = Carbon::now();
        } else {
            $Invoice->status = 'Partial_Paid';
        }
    
        $Invoice->save();

        // Update sales status based on invoice payment status
        if ($Invoice->status === 'Paid') {

            SalesTuli::where('invoice_issued_id', $id)
                ->update([
                    'status' => 'sold_invoiced_completed'
                ]);

        } elseif ($Invoice->status === 'Partial_Paid') {

            SalesTuli::where('invoice_issued_id', $id)
                ->update([
                    'status' => 'sold_invoiced_partial'
                ]);
        }
    
        // Create a new record in InvoicePaymentDetail
        InvoicePaymentDetailsTuli::create([
            'invoice_no' => $Invoice->id,
            'amount_submitted' => $newAmount,
            'date_payed' => Carbon::now(),
            'status' => 'Paid',
            'payer_id' => auth()->user()->first_name, // full name
            'receipt_number' => 'REC-' . time() . '-' . $Invoice->id,
            'channel' => 'Manual',
        ]);
    
        // Update Invoice Items if fully paid
        if ($Invoice->status === 'Paid') {
            $InvoiceItems = InvoiceItemsTuli::where('invoice_id', $id)->get();
            foreach ($InvoiceItems as $item) {
                $item->status = 'Paid';
                $item->paid_by = auth()->user()->first_name;
                $item->date_paid = Carbon::now();
                $item->save();
            }
        }
    
        return redirect()->back()->with('success', 'Invoice updated and payment recorded successfully.');
    }

    public function EdititemInvoiceQty(Request $request)
{
    // 1. Check product stock availability
    $item = ProductsTuli::where('id', $request->product_id)
        ->where('qty_remained', '>=', $request->new_qty)
        ->first();

    if (!$item) {
        return redirect()->back()
            ->with('error', 'Insufficient stock available. Check remaining quantity or adjust quantity!');
    }

    // 2. Update invoice item quantity
    $InvoiceItem = InvoiceItemsTuli::find($request->item_id);

    if (!$InvoiceItem) {
        return redirect()->back()
            ->with('error', 'Invoice item not found.');
    }
    

    $InvoiceItem->qty = $request->new_qty;
    $InvoiceItem->save();

    $sale = SalesTuli::where('invoice_issued_id', $InvoiceItem->invoice_id)
    ->where('product_id', $InvoiceItem->product_id)
    ->first();

    if ($sale) {
        $sale->quantity = $request->new_qty;
        // $sale->status = 'sold_invoiced_completed';
        $sale->save();
    }

    // 3. Fetch related invoice
    $invoice = $InvoiceItem->invoices_tuli;

    if (!$invoice) {
        return redirect()->back()
            ->with('error', 'Invoice not found.');
    }

    // 4. Recalculate invoice total
    $total_invoice_amount = $invoice->invoice_items_tuli()
        ->sum(\DB::raw('qty * price'));

    // 5. Recalculate remaining amount
    $amount_paid = $invoice->amount_paid ?? 0;
    $amount_remained = $total_invoice_amount - $amount_paid;

    // 6. Update invoice
    $invoice->update([
        'total_invoice_amount' => $total_invoice_amount,
        'amount_paid' => $amount_paid,
        'amount_remained' => $amount_remained,
    ]);

    return redirect()->back()
        ->with('success', 'Quantity changed and invoice updated successfully.');
}

public function EdititemInvoice(Request $request)
{   
    // 1. Find the invoice item
    $InvoiceItem = InvoiceItemsTuli::find($request->item_id);

    if (!$InvoiceItem) {
        return redirect()->back()->with('error', 'Invoice item not found.');
    }

    // 2. Update the invoice item with discount
    $InvoiceItem->price = $request->discount_amount; 
    $InvoiceItem->discount = 'yes';
    $InvoiceItem->discounte_by = intval(Auth::user()->id);
    $InvoiceItem->date_discounted = now();
    $InvoiceItem->save();

    $sale = SalesTuli::where('invoice_issued_id', $InvoiceItem->invoice_id)
    ->where('product_id', $InvoiceItem->product_id)
    ->first();

if ($sale) {
    $sale->original_price = $sale->original_price ?: $sale->selling_price;
    $sale->discount_amount = $sale->selling_price - $request->discount_amount;
    $sale->selling_price = $request->discount_amount;
    $sale->save();
}

    // 3. Get the related invoice
    $invoice = $InvoiceItem->invoices_tuli;

    if (!$invoice) {
        return redirect()->back()->with('error', 'Invoice not found.');
    }

    // 4. Recalculate totals
    $total_invoice_amount = $invoice->invoice_items_tuli()
        ->sum(\DB::raw('qty * price'));

    // 5. Update invoice financial fields
    $amount_paid = $invoice->amount_paid ?? 0;
    $amount_remained = $total_invoice_amount - $amount_paid;

    // 6. Save updated invoice totals
    $invoice->update([
        'total_invoice_amount' => $total_invoice_amount,
        'amount_paid' => $amount_paid,
        'amount_remained' => $amount_remained,
    ]);

    return redirect()->back()
        ->with('success', 'Discount applied and invoice updated successfully.');
}
public function salesReport(Request $request)
{
    $query = SalesTuli::with([
        'products_tuli',
        'customers_tuli',
        'invoices_tuli'
    ]);

    // Product
    if ($request->filled('product_name')) {
        $query->whereHas('products_tuli', function ($q) use ($request) {
            $q->where('product_name', 'like', '%' . $request->product_name . '%');
        });
    }

    // Customer
    if ($request->filled('customer_name')) {
        $query->whereHas('customers_tuli', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->customer_name . '%');
        });
    }

    // Date
    if ($request->filled('start_date') && $request->filled('end_date')) {

        $query->whereBetween('date_sold', [
            Carbon::parse($request->start_date)->startOfDay(),
            Carbon::parse($request->end_date)->endOfDay(),
        ]);
    }

    // Month
    if ($request->filled('start_month') && $request->filled('end_month')) {

        $query->whereBetween('date_sold', [
            Carbon::parse($request->start_month)->startOfMonth(),
            Carbon::parse($request->end_month)->endOfMonth(),
        ]);
    }

    // Status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Totals
    $totalQty = (clone $query)->sum('quantity');

    $totalAmount = (clone $query)
        ->selectRaw('SUM(quantity * selling_price) as total')
        ->value('total');

    $sales = $query
        ->latest('date_sold')
        ->paginate(50);

    return view(
        'admin.tuli_sales_management.sales-report',
        compact('sales', 'totalQty', 'totalAmount')
    );
}
    
}
