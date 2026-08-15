<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Sale;
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
    public function salesReport(Request $request)
    {
        $query = InvoiceItem::query();
    
        // Apply filters
        if ($request->filled('product_name')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('product_name', 'like', '%' . $request->product_name . '%');
            });
        }
    
        if ($request->filled('customer_name')) {
            $query->whereHas('invoice.customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer_name . '%');
            });
        }
    
        // Fix date filtering to include full day range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
    
            $query->whereHas('invoice', function ($q) use ($start, $end) {
                $q->whereBetween('invoice_date', [$start, $end]);
            });
        }
    
        if ($request->filled('start_month') && $request->filled('end_month')) {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->whereBetween('invoice_date', [
                    Carbon::parse($request->start_month . '-01')->startOfMonth(),
                    Carbon::parse($request->end_month . '-01')->endOfMonth()
                ]);
            });
        }
    
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    
        // Clone for totals before pagination
        $totalsQuery = (clone $query);
        $totalQty = $totalsQuery->sum('qty');
    
        // Calculate total amount
        $totalAmount = (clone $query)->selectRaw('SUM(qty * price) as total')->value('total');
    
        // Order by ID descending before pagination
        $sales = $query->orderBy('id', 'desc')->paginate(50);
    
        return view('admin.sales_management.sales-report', compact('sales', 'totalQty', 'totalAmount'));
    }
    

}
