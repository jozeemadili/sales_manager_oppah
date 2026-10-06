<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Sale;
use App\Models\Store;
use App\Http\Livewire\SalesManagement\QuickSale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Route;
use Carbon\Carbon;

class CustomersController extends Controller
{
    public function get()
    {
        if(Auth::user()->email == 'jchaboma@oppah01.co.tz')
        {
            $Branch = Customer::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
        }else{
            $Branch = Customer::where('company_id',Auth::user()->company_id)->where('store_id',Auth::user()->office_location)->orderBy('id','desc')->paginate(10);
        }
            
           return view('admin.sales_management.customers-registration',['Branch' => $Branch]);
    }
    public function searchCustomer(Request $request)
    {
        $loans_datails = Customer::query();

            // If all details are provided, prioritize finding an exact match for all details
            if (!empty($request->phone_number) && !empty($request->id_number) && !empty($request->cname)) {
                $loans_datails->where('phone', $request->phone_number)
                            ->where('email', $request->id_number)
                            ->where('name', 'like', '%' . $request->cname . '%');
                           
            } else {
                // Otherwise, apply conditions based on non-empty inputs
                if (!empty($request->phone_number)) {
                    $loans_datails->orWhere('phone', $request->phone_number);
                }

                if (!empty($request->id_number)) {
                    $loans_datails->orWhere('email', $request->id_number);
                }

                if (!empty($request->cname)) {
                    $loans_datails->where('name', 'like', '%' . $request->cname . '%');
                }
                
            }

            $loans_datails = $loans_datails->orderBy('ID', 'desc')->paginate(10);

        // $Branch = Customer::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
        return view('admin.sales_management.customers-registration',['Branch' => $loans_datails]);
    }
    public function downloadInvoicesCSV(Request $request)
    {
        $fileName = 'sales_report.csv';

        // Query with filters
        $query = InvoiceItem::with(['invoice.customer', 'invoice', 'product']);

        if ($request->filled('customer_name')) {
            $query->whereHas('invoice.customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer_name . '%');
            });
        }
// Apply filters
if ($request->filled('product_name')) {
    $query->whereHas('product', function ($q) use ($request) {
        $q->where('product_name', 'like', '%' . $request->product_name . '%');
    });
}
       

        if ($request->filled('status')) {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->whereBetween('created_at', [$request->start_date, $request->end_date]);
            });
        }

        if ($request->filled('start_month') && $request->filled('end_month')) {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->whereYear('created_at', date('Y', strtotime($request->start_month)))
                    ->whereMonth('created_at', '>=', date('m', strtotime($request->start_month)))
                    ->whereMonth('created_at', '<=', date('m', strtotime($request->end_month)));
            });
        }

        if ($request->filled('actioned_by')) {
            $query->where('actioned_by', 'like', '%' . $request->actioned_by . '%');
        }

        if ($request->filled('paid_by')) {
            $query->where('paid_by', 'like', '%' . $request->paid_by . '%');
        }

        $sales = $query->get();

        // CSV headers
        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        return response()->streamDownload(function () use ($sales) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, ['Product/Service', 'Type', 'Customer', 'Control No', 'Invoice Date', 'Payment Date', 'Total Quantity', 'Total Amount', 'Status', 'Payment Type', 'Paid By']);

            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->product->product_name,
                    $sale->product->service_type,
                    $sale->invoice->customer->name ?? 'N/A',
                    $sale->invoice->control_no ?? 'N/A',
                    $sale->invoice->created_at ? $sale->invoice->created_at->format('d/m/Y H:i:s') : 'N/A',
                    $sale->invoice->paid_date ? $sale->invoice->paid_date->format('d/m/Y H:i:s') : 'N/A',
                    $sale->qty,
                    number_format($sale->qty * $sale->price, 2),
                    $sale->invoice->status ?? 'N/A',
                    $sale->actioned_by ?? 'N/A',
                    $sale->paid_by ?? 'N/A',
                ]);
            }

            fclose($file);
        }, $fileName, $headers);
    }
    public function register(Request $request)
    {   
        $existingCustomer = Customer::where('name', $request->name)->first();

        if ($existingCustomer) {
            return redirect()->route('customers-management')->with('success', 'Customer with this name already exists <b>'.strtoupper($request->name).' </b> Seems You submitted more than once Details');
        }
      
        // Proceed to create new customer
        $user = Customer::create([
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
            'store_id'      => intval(Auth::user()->office_location),
            
        ]);
        return redirect()->route('customers-management')->with('success', 'Customer With name <b>'.strtoupper($request->name).' </b> Successfully Registered  : ');
    }
    // public function profile($id)
    // {
       
    //         $Customers= Customer::find($id);
    //         $Invoice= Invoice::where('customer_id',$id)->with('invoice_items')->with('invoice_payment_details')->get();
    //         $sales = Sale::where('customer_id',$id)->paginate(10);
    //         return view('admin.sales_management.customer-profile',['Customers' => $Customers,'Invoice' => $Invoice,'sales'=>$sales]);
       
    // }
    public function profile($id)
{
    $customer = Customer::findOrFail($id);

    $invoices = Invoice::where('customer_id', $id)
        ->with('invoice_items', 'invoice_payment_details', 'Customer', 'User')
        ->get();

    $sales = Sale::where('customer_id', $id)->paginate(10);

    // ===== Grand Totals =====
    $grandTotalQty = 0;
    $grandTotalAmount = 0;
    $grandTotalPaid = 0;
    $grandTotalRemained = 0;

    foreach ($invoices as $invoice) {
        $invoiceQty = $invoice->invoice_items->sum('qty');

        $grandTotalQty += $invoiceQty;
        $grandTotalAmount += $invoice->total_invoice_amount;
        $grandTotalPaid += $invoice->amount_paid;
        $grandTotalRemained += $invoice->amount_remained;
    }

    return view('admin.sales_management.customer-profile', [
        'Customers' => $customer,
        'Invoice' => $invoices,
        'sales' => $sales,
        'grandTotalQty' => $grandTotalQty,
        'grandTotalAmount' => $grandTotalAmount,
        'grandTotalPaid' => $grandTotalPaid,
        'grandTotalRemained' => $grandTotalRemained,
    ]);
}

    public function invoiceReport()
    {
        if(Route::currentRouteName()=='invoices-pending')
        {
            $Invoice= Invoice::where('status','Pending')->with('invoice_items')->get();
        }
        elseif(Route::currentRouteName()=='invoices-confermed')
        {
            $Invoice= Invoice::where('status','Confirmed')->with('invoice_items')->get();
        }
        elseif(Route::currentRouteName()=='invoices-paid')
        {
            $Invoice= Invoice::where('status','Paid')->with('invoice_items')->get();
        }
            
            
            // $sales = Sale::where('customer_id',$id)->paginate(10);
            return view('admin.sales_management.invoice-reports',['Invoice' => $Invoice]);
       
    }
    /**
     * Sales report (invoice items). Totals are split so they can be checked
     * against the dashboard graph:
     *  - Invoiced value  = qty x price of the listed items (paid or not)
     *  - Amount received = amount_paid on the matching invoices
     *  - Quick sales     = walk-in sales (not on invoices)
     * For the Mzinga store, "Amount received + Quick sales" equals the
     * dashboard Payment Trends sales for the same dates.
     */
    public function salesReport(Request $request)
    {
        [$from, $to] = $this->salesReportRange($request);
        $mainStore = Store::mainStore();
        $storeId = $request->input('store_id', $mainStore ? $mainStore->id : 'all');
        $storeId = $storeId === 'all' || $storeId === '' ? null : (int) $storeId;
        $companyId = Auth::user()->company_id;

        // Filters on the invoice itself (date, store, status, customer).
        $invoiceFilter = function ($q) use ($request, $from, $to, $storeId, $companyId) {
            $q->when($companyId != 1, fn ($q) => $q->where('company_id', $companyId))
              ->when($from, fn ($q) => $q->whereBetween('invoice_date', [$from, $to]))
              ->when($storeId, fn ($q) => $q->whereHas('customer', fn ($c) => $c->where('store_id', $storeId)))
              ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
              ->when($request->filled('customer_name'), fn ($q) => $q->whereHas('customer', fn ($c) => $c->where('name', 'like', '%'.$request->customer_name.'%')));
        };
        $productFilter = fn ($q) => $q->where('product_name', 'like', '%'.$request->product_name.'%');

        $query = InvoiceItem::with(['product', 'invoice.customer'])
            ->whereHas('invoice', $invoiceFilter)
            ->when($request->filled('product_name'), fn ($q) => $q->whereHas('product', $productFilter));

        if ($request->input('export') === 'csv') {
            return $this->salesReportCsv((clone $query)->orderBy('id', 'desc'));
        }

        $totalQty = (clone $query)->sum('qty');
        $totalAmount = (clone $query)->selectRaw('SUM(qty * price) as total')->value('total');

        $amountReceived = Invoice::where($invoiceFilter)
            ->when($request->filled('product_name'), fn ($q) => $q->whereHas('invoice_items.product', $productFilter))
            ->sum('amount_paid');

        // Walk-in sales are always paid and have no customer, so they only
        // count when no customer / non-paid status filter is chosen.
        $quickSales = 0;
        $quickQty = 0;
        if (!$request->filled('customer_name') && in_array($request->input('status'), [null, '', 'Paid'], true)) {
            $quick = DB::table('sales as s')
                ->join('products as p', 'p.id', '=', 's.product_id')
                ->where('s.customer_id', QuickSale::WALK_IN_CUSTOMER)
                ->whereIn('s.status', QuickSale::SOLD_STATUSES)
                ->when($companyId != 1, fn ($q) => $q->where('s.company_id', $companyId))
                ->when($from, fn ($q) => $q->whereBetween('s.date_sold', [$from, $to]))
                ->when($storeId, fn ($q) => $q->where('p.store_id', $storeId))
                ->when($request->filled('product_name'), fn ($q) => $q->where('p.product_name', 'like', '%'.$request->product_name.'%'))
                ->selectRaw('COALESCE(SUM(s.selling_price * s.quantity), 0) as amount, COALESCE(SUM(s.quantity), 0) as qty')
                ->first();
            $quickSales = (float) $quick->amount;
            $quickQty = (float) $quick->qty;
        }

        $sales = $query->orderBy('id', 'desc')->paginate(50)->withQueryString();
        $stores = Store::orderBy('name')->get(['id', 'name']);

        return view('admin.sales_management.sales-report', compact(
            'sales', 'totalQty', 'totalAmount', 'amountReceived', 'quickSales', 'quickQty', 'stores', 'storeId'
        ));
    }

    // Date range from either the date pair or the month pair (dates win).
    private function salesReportRange(Request $request)
    {
        if ($request->filled('start_date') && $request->filled('end_date')) {
            return [Carbon::parse($request->start_date)->startOfDay(), Carbon::parse($request->end_date)->endOfDay()];
        }
        if ($request->filled('start_month') && $request->filled('end_month')) {
            return [Carbon::parse($request->start_month.'-01')->startOfMonth(), Carbon::parse($request->end_month.'-01')->endOfMonth()];
        }

        return [null, null];
    }

    private function salesReportCsv($query)
    {
        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Invoice No', 'Product', 'Customer', 'Store', 'Invoice Date', 'Quantity', 'Price', 'Total Amount', 'Invoice Status', 'Date Paid', 'Paid By']);
            $query->chunk(500, function ($items) use ($out) {
                foreach ($items as $item) {
                    $invoice = $item->invoice;
                    fputcsv($out, [
                        $item->invoice_id,
                        optional($item->product)->product_name,
                        optional(optional($invoice)->customer)->name,
                        optional(optional(optional($invoice)->customer)->store)->name,
                        optional($invoice)->invoice_date,
                        $item->qty,
                        $item->price,
                        $item->qty * $item->price,
                        optional($invoice)->status,
                        optional($invoice)->date_paid,
                        optional($invoice)->paid_by,
                    ]);
                }
            });
            fclose($out);
        }, 'sales-report-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

}
