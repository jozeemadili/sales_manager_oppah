<?php

namespace App\Http\Livewire\Components\Reports;

use App\Models\Invoice;
use App\Models\InvoicePaymentDetail;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Summary extends Component
{
    public $summary = [];
    public $sales = [];
    public $storeId = 'all';
    public $stores = [];
    public $expenseBreakdown = [];
    public $expensesChart = [];

    public function mount()
    {
        $this->stores = Store::all(); // Fetch all stores for dropdown
        $this->loadData();
    }

    public function updatedStoreId()
    {
        $this->loadData(); // Reload data when store changes
    }

    private function loadData()
    {
        $companyId = Auth::user()->company_id;

        // --- PRODUCTS SUMMARY ---
        $productQuery = Product::where('status', 'Active');

        if ($companyId != 1) {
            $productQuery->where('company_id', $companyId);
        }

        if ($this->storeId !== 'all') {
            $productQuery->where('store_id', $this->storeId);
        }

        $sumProductCost = (clone $productQuery)->sum(DB::raw('purchasing_price * qty_remained'));
        $sumProduct = $productQuery->sum(DB::raw('selling_price * qty_remained'));

        // --- EXPENSES (recorded per inventory on inventory/preview/{id}) ---
        // Only MBAO-type expenses; store comes from the inventory they were recorded on.
        $expenseQuery = DB::table('expenses_records as r')
            ->join('expenses as e', 'e.id', '=', 'r.expense_id')
            ->join('inventories as i', 'i.id', '=', 'r.inventory_id')
            ->where('r.status', 'Active')
            ->where('e.to_be_used', 'MBAO')
            ->where('r.reg_at', '<=', Carbon::now());

        if ($companyId != 1) {
            $expenseQuery->where('r.company_id', $companyId);
        }

        if ($this->storeId !== 'all') {
            $expenseQuery->where('i.store_id', $this->storeId);
        }

        $expensesToDate = (clone $expenseQuery)->sum('r.amount_used');
        $expensesMonth = (clone $expenseQuery)->where('r.reg_at', '>=', Carbon::now()->startOfMonth())->sum('r.amount_used');
        $expensesToday = (clone $expenseQuery)->where('r.reg_at', '>=', Carbon::today())->sum('r.amount_used');

        $this->expenseBreakdown = (clone $expenseQuery)
            ->groupBy('e.e_name')
            ->orderByDesc('total')
            ->get(['e.e_name as name', DB::raw('SUM(r.amount_used) as total'), DB::raw('COUNT(*) as entries')])
            ->map(fn ($row) => ['name' => $row->name, 'total' => (float) $row->total, 'entries' => (int) $row->entries])
            ->all();

        // --- CUSTOMERS ---
        $customerQuery = Customer::query();
        if ($companyId != 1) {
            $customerQuery->where('company_id', $companyId);
        }
        if ($this->storeId !== 'all') {
            $customerQuery->where('store_id', $this->storeId);
        }

        // --- INVOICES ---
        $invoiceQuery = Invoice::query();
        if ($companyId != 1) {
            $invoiceQuery->where('company_id', $companyId);
        }
        if ($this->storeId !== 'all') {
            $invoiceQuery->whereHas('customer', function (Builder $q) {
                $q->where('store_id', $this->storeId);
            });
        }

        // Today's date range
        $startTime = Carbon::today()->startOfDay();
        $endTime = Carbon::today()->endOfDay();

        $invoiceTodayQuery = (clone $invoiceQuery)
            ->whereBetween('invoice_date', [$startTime, $endTime]);

        $sumToday = InvoicePaymentDetail::whereDate('date_payed', Carbon::today())
            ->when($this->storeId !== 'all', function ($q) {
                $q->whereHas('invoice.customer', fn($query) => $query->where('store_id', $this->storeId));
            })
            ->sum('amount_submitted');

        // --- SUMMARY DATA ---
        $this->summary = [
            'sumProduct'       => number_format($sumProduct, 0, '.', ','),
            'sumProductCost'   => number_format($sumProductCost, 0, '.', ','),
            'expenses_to_date' => number_format($expensesToDate, 0, '.', ','),
            'expenses_month'   => number_format($expensesMonth, 0, '.', ','),
            'expenses_today'   => number_format($expensesToday, 0, '.', ','),
            'generated_amount' => number_format($invoiceTodayQuery->sum('total_invoice_amount'), 2, '.', ','),
            'paid_amount'      => number_format($sumToday, 2, '.', ','),
            'unpaid_amount'    => number_format($invoiceTodayQuery->sum('amount_remained'), 2, '.', ','),
            'unpaid_overall'   => number_format($invoiceQuery->sum('amount_remained'), 2, '.', ','),
            'pending'          => number_format($invoiceTodayQuery->where('status','Pending')->count(), 0, '.', ','),
            'confirmed'        => number_format($invoiceTodayQuery->where('status','Confirmed')->count(), 0, '.', ','),
            'paid'             => number_format($invoiceTodayQuery->where('status','Paid')->count(), 0, '.', ','),
            'expired'          => number_format($invoiceTodayQuery->where('status','Expired')->count(), 0, '.', ','),
            'total_customers'  => number_format($customerQuery->count(), 0, '.', ','),
            'active_customers' => number_format($customerQuery->where('status','Active')->count(), 0, '.', ','),
            'inactive_customers'=> number_format($customerQuery->where('status','Inactive')->count(), 0, '.', ','),
        ];

        // --- SALES CHART DATA ---
        $invoiceSalesQuery = Invoice::select(
                DB::raw('MONTH(invoice_date) as month'),
                DB::raw('SUM(amount_paid) as amount')
            )
            ->whereYear('invoice_date', date('Y'));

        if ($companyId != 1) {
            $invoiceSalesQuery->where('company_id', $companyId);
        }

        if ($this->storeId !== 'all') {
            $invoiceSalesQuery->whereHas('customer', function (Builder $q) {
                $q->where('store_id', $this->storeId);
            });
        }

        $dbData = $invoiceSalesQuery->groupBy(DB::raw('MONTH(invoice_date)'))
            ->orderBy(DB::raw('MONTH(invoice_date)'))
            ->get();

        $salesData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthData = $dbData->where('month', $month)->pluck('amount');
            $salesData[] = $monthData->isNotEmpty() ? (float)$monthData->first() : 0;
        }

        $this->sales = $salesData;

        // --- EXPENSES CHART DATA (same scope as the expense cards) ---
        $expenseRows = (clone $expenseQuery)
            ->whereYear('r.reg_at', date('Y'))
            ->groupBy(DB::raw('MONTH(r.reg_at)'))
            ->get([DB::raw('MONTH(r.reg_at) as month'), DB::raw('SUM(r.amount_used) as amount')]);

        $expenseData = [];
        for ($month = 1; $month <= 12; $month++) {
            $expenseData[] = (float) optional($expenseRows->firstWhere('month', $month))->amount;
        }

        $this->expensesChart = $expenseData;

        // Emit event to update chart
        $this->emit('salesUpdated', $this->sales, $this->expensesChart);
    }

    public function render()
    {
        return view('livewire.components.reports.summary');
    }
}
