<?php

namespace App\Http\Livewire\Components\Reports;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\LOANAPPLICATION;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\Invoice;
use App\Models\Customer;
use Carbon\Carbon;


class Summary extends Component
{
    public $summary;

    public function render()
    {
        $sumProduct = Product::where('status', 'Active')
            ->sum(DB::raw('selling_price * qty_remained'));
    
        $companyId = Auth::user()->company_id;
    
        // Build the base queries
        $invoiceQuery = Invoice::query();
        $customerQuery = Customer::query();
    
        if ($companyId != 1) {
            $invoiceQuery->where('company_id', $companyId);
            $customerQuery->where('company_id', $companyId);
        }
    
       
// Start of today
$startTime = Carbon::today()->startOfDay(); // 00:00:00 today

// End of today
$endTime = Carbon::today()->endOfDay(); // 23:59:59 today

// Dynamic query for today
$invoiceTodayQuery = (clone $invoiceQuery)
    ->whereBetween('invoice_date', [$startTime, $endTime]);
    
        // 💰 Today's financial summaries
        $totalGeneratedAmount = (clone $invoiceTodayQuery)->sum('total_invoice_amount');
        $totalPaidAmount      = (clone $invoiceTodayQuery)->sum('amount_paid');
        $totalUnpaidAmount    = (clone $invoiceTodayQuery)->sum('amount_remained');
    
        // 💰 Overall unpaid amount (not limited to today)
        $totalUnpaidOverall = (clone $invoiceQuery)->sum('amount_remained');
    
        // 📊 Status counts
        $pendingInvoices   = (clone $invoiceTodayQuery)->where('status', 'Pending')->count();
        $confirmedInvoices = (clone $invoiceTodayQuery)->where('status', 'Confirmed')->count();
        $paidInvoices      = (clone $invoiceTodayQuery)->where('status', 'Paid')->count();
        $expiredInvoices   = (clone $invoiceTodayQuery)->where('status', 'Expired')->count();
    
        // 👥 Customer summaries
        $totalCustomers    = (clone $customerQuery)->count();
        $activeCustomers   = (clone $customerQuery)->where('status', 'Active')->count();
        $inactiveCustomers = (clone $customerQuery)->where('status', 'Inactive')->count();
    
        // 🧾 Prepare formatted summary data
        $this->summary = [
            'generated_amount' => number_format($totalGeneratedAmount, 2, '.', ','),
            'paid_amount'      => number_format($totalPaidAmount, 2, '.', ','),
            'unpaid_amount'    => number_format($totalUnpaidAmount, 2, '.', ','),
            'unpaid_overall'   => number_format($totalUnpaidOverall, 2, '.', ','), // 🌟 NEW FIELD
    
            'pending'          => number_format($pendingInvoices, 0, '.', ','),
            'confirmed'        => number_format($confirmedInvoices, 0, '.', ','),
            'paid'             => number_format($paidInvoices, 0, '.', ','),
            'expired'          => number_format($expiredInvoices, 0, '.', ','),
    
            'total_customers'  => number_format($totalCustomers, 0, '.', ','),
            'active_customers' => number_format($activeCustomers, 0, '.', ','),
            'inactive_customers' => number_format($inactiveCustomers, 0, '.', ','),
            'sumProduct'       => number_format($sumProduct, 0, '.', ','),
        ];
    
        return view('livewire.components.reports.summary');
    }


}
