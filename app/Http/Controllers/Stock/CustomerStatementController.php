<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use App\TIRAClient\Scripts\Classes\Utils;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

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
        })->except('publicPdf');
    }

    public function show($id)
    {
        return view('admin.sales_management.customer-statement', $this->statement($id));
    }

    // How long the QR link printed on a reminder keeps working.
    const QR_LINK_DAYS = 90;

    public function pdf($id)
    {
        $data = $this->statement($id);

        return $this->renderPdf($data)->download($this->fileName($data));
    }

    /**
     * Opened by scanning the QR code on a printed reminder: no login, but the
     * link is signed (and expires), so customer ids cannot be guessed or edited.
     * Shows the current statement inline in the phone's browser.
     */
    public function publicPdf($id)
    {
        $data = $this->statement($id, false);

        return $this->renderPdf($data)->stream($this->fileName($data));
    }

    private function renderPdf(array $data)
    {
        $link = URL::temporarySignedRoute('customer-statement-public', now()->addDays(self::QR_LINK_DAYS), ['id' => $data['customer']->id]);
        $data['qrcode'] = Utils::svgToBase64(Utils::getQrcode($link, 160, 'svg'));
        $data['qrExpires'] = now()->addDays(self::QR_LINK_DAYS);

        // Subset the font so the file stays small enough to share on WhatsApp.
        return Pdf::setOption(['isFontSubsettingEnabled' => true])
            ->loadView('admin.sales_management.customer-statement-pdf', $data);
    }

    private function fileName(array $data)
    {
        return 'statement_'.preg_replace('/[^a-z0-9]+/', '_', strtolower($data['customer']->name)).'_'.now()->format('Ymd').'.pdf';
    }

    private function statement($id, $checkCompany = true)
    {
        $customer = Customer::findOrFail($id);

        $companyId = Auth::check() ? Auth::user()->company_id : $customer->company_id;
        if ($checkCompany) {
            abort_unless($companyId == 1 || $customer->company_id == $companyId, 404);
        }

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
