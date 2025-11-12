<?php

namespace App\Http\Livewire\Components\Reports;

use App\Models\InvoiceItem;
use App\Models\LOANAPPLICATION;
use App\Models\Quotation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\Invoice;

class Salescharts extends Component
{
    public $sales;
    public $accessAll;

    public function render()
    {
        $data = [];
        $companyId = Auth::user()->company_id;

        // Base query from invoices
        $invoiceQuery = Invoice::select(
            DB::raw('MONTH(invoice_date) as month'),
            DB::raw('SUM(amount_paid) as amount')
        )
        ->whereYear('invoice_date', date('Y'))
        // ->where('status', 'Paid')
        ->groupBy(DB::raw('MONTH(invoice_date)'))
        ->orderBy(DB::raw('MONTH(invoice_date)'));

        // Filter by company (admin = all)
        if ($companyId != 1) {
            $invoiceQuery->where('company_id', $companyId);
        }

        $dbData = $invoiceQuery->get();

        // Build array of 12 months (Jan–Dec)
        for ($month = 1; $month <= 12; $month++) {
            $monthData = $dbData->where('month', $month)->pluck('amount');
            $data[] = $monthData->isNotEmpty() ? (float)$monthData->first() : 0;
        }

        $this->sales = $data;

        return view('livewire.components.reports.salescharts');
    }

}
