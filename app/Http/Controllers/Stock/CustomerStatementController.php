<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Customer statement of unpaid invoices (Mbao), on screen and as a PDF
 * payment reminder to hand or send to the customer.
 */
class CustomerStatementController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            abort_unless($user->role === 'Mbao' || $user->hasFullAccess(), 403);

            return $next($request);
        });
    }

    public function show($id)
    {
        return view('admin.sales_management.customer-statement', $this->statement($id));
    }

    public function pdf($id)
    {
        $data = $this->statement($id);

        // Subset the font so the file stays small enough to share on WhatsApp.
        $pdf = Pdf::setOption(['isFontSubsettingEnabled' => true])
            ->loadView('admin.sales_management.customer-statement-pdf', $data);

        $fileName = 'statement_'.preg_replace('/[^a-z0-9]+/', '_', strtolower($data['customer']->name)).'_'.now()->format('Ymd').'.pdf';

        return $pdf->download($fileName);
    }

    private function statement($id)
    {
        $customer = Customer::findOrFail($id);

        $companyId = Auth::user()->company_id;
        abort_unless($companyId == 1 || $customer->company_id == $companyId, 404);

        $invoices = Invoice::where('customer_id', $customer->id)
            ->where('amount_remained', '>', 0)
            ->orderBy('invoice_date')
            ->get();

        return [
            'customer' => $customer,
            'company' => Company::find($customer->company_id) ?? Company::find($companyId),
            'invoices' => $invoices,
            'totals' => [
                'total' => (float) $invoices->sum('total_invoice_amount'),
                'paid' => (float) $invoices->sum('amount_paid'),
                'balance' => (float) $invoices->sum('amount_remained'),
            ],
            'generatedAt' => Carbon::now(),
        ];
    }

    // Same numbering as the invoice PDF (OP000{id}/025).
    public static function invoiceNumber($invoice)
    {
        return 'OP000'.$invoice->id.'/025';
    }
}
