<?php

namespace App\Http\Livewire\Components\Reports;

use App\Models\LOANAPPLICATION;
use App\Models\Quotation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\Invoice;

class Quotationstatus extends Component
{
    public $products_performance;
    public $accessAll;

    public function render()
    {
        $data = [];
        $companyId = Auth::user()->company_id;

        // 🧾 Base query: count invoices by status (this year)
        $invoiceQuery = Invoice::select(
            DB::raw('COUNT(*) as count'),
            'status'
        )
        ->whereYear('invoice_date', date('Y'))
        ->groupBy('status');

        // 🔐 Restrict to company if not admin
        if ($companyId != 1) {
            $invoiceQuery->where('company_id', $companyId);
        }

        $performance = $invoiceQuery->get()->toArray();

        // 🔄 Format results for chart or table
        foreach ($performance as $p) {
            $data[] = [ucfirst(strtolower($p['status'])), $p['count']];
        }

        $this->products_performance = $data;

        return view('livewire.components.reports.quotationstatus');
    }
}

