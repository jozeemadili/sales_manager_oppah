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
    public $dailyBreakdown = [];
    public $dailyChart = [];
    public $chartPeriod = 'year';
    public $chartCategories = [];
    public $chartStoreName = '';

    // Payment Trends always shows this store (matched by name).
    const CHART_STORE_NAME = 'MZINGA';

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

        // --- DAILY EXPENSES (running costs, not tied to an inventory) ---
        $dailyQuery = DB::table('daily_expenses as d')
            ->join('daily_expense_types as t', 't.id', '=', 'd.expense_type_id')
            ->where('d.status', 'Active')
            ->where('d.expense_date', '<=', Carbon::today()->toDateString());

        if ($companyId != 1) {
            $dailyQuery->where('d.company_id', $companyId);
        }

        if ($this->storeId !== 'all') {
            $dailyQuery->where('d.store_id', $this->storeId);
        }

        $monthStart = Carbon::now()->startOfMonth()->toDateString();
        $dailyToday = (clone $dailyQuery)->where('d.expense_date', Carbon::today()->toDateString())->sum('d.amount');
        $dailyMonth = (clone $dailyQuery)->where('d.expense_date', '>=', $monthStart)->sum('d.amount');

        $this->dailyBreakdown = (clone $dailyQuery)
            ->where('d.expense_date', '>=', $monthStart)
            ->groupBy('t.name')
            ->orderByDesc('total')
            ->get(['t.name as name', DB::raw('SUM(d.amount) as total'), DB::raw('COUNT(*) as entries')])
            ->map(fn ($row) => ['name' => $row->name, 'total' => (float) $row->total, 'entries' => (int) $row->entries])
            ->all();

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
            'daily_today'      => number_format($dailyToday, 0, '.', ','),
            'daily_month'      => number_format($dailyMonth, 0, '.', ','),
            'all_expenses_month' => number_format($expensesMonth + $dailyMonth, 0, '.', ','),
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

        $this->loadChart();
    }

    public function updatedChartPeriod()
    {
        if (!in_array($this->chartPeriod, ['week', 'month', 'year'])) {
            $this->chartPeriod = 'year';
        }

        $this->loadChart();
    }

    /**
     * Payment Trends: sales vs inventory expenses vs daily expenses.
     * Always scoped to the Mzinga store (independent of the store dropdown),
     * grouped per day for week/month and per month for year.
     */
    private function loadChart()
    {
        $companyId = Auth::user()->company_id;
        $store = Store::where('name', 'like', '%'.self::CHART_STORE_NAME.'%')->first();
        $storeId = optional($store)->id;
        $this->chartStoreName = $store ? $store->name : 'All Stores';

        if ($this->chartPeriod === 'year') {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();
            $bucketSql = fn ($col) => "MONTH($col)";
            $keys = range(1, 12);
            $this->chartCategories = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        } else {
            $start = $this->chartPeriod === 'week' ? Carbon::today()->subDays(6) : Carbon::now()->startOfMonth();
            $end = $this->chartPeriod === 'week' ? Carbon::today()->endOfDay() : Carbon::now()->endOfMonth();
            $bucketSql = fn ($col) => "DATE($col)";
            $keys = [];
            $this->chartCategories = [];
            for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
                $keys[] = $day->toDateString();
                $this->chartCategories[] = $this->chartPeriod === 'week' ? $day->format('D d') : $day->format('d');
            }
        }

        $series = function ($query, $col, $sumCol) use ($bucketSql, $keys, $start, $end) {
            $rows = $query->whereBetween($col, [$start->toDateTimeString(), $end->toDateTimeString()])
                ->groupBy(DB::raw($bucketSql($col)))
                ->get([DB::raw($bucketSql($col).' as bucket'), DB::raw("SUM($sumCol) as amount")])
                ->pluck('amount', 'bucket');

            return array_map(fn ($key) => (float) ($rows[$key] ?? 0), $keys);
        };

        $sales = Invoice::query()
            ->when($companyId != 1, fn ($q) => $q->where('company_id', $companyId))
            ->when($storeId, fn ($q) => $q->whereHas('customer', fn (Builder $c) => $c->where('store_id', $storeId)))
            ->toBase();

        $inventoryExpenses = DB::table('expenses_records as r')
            ->join('expenses as e', 'e.id', '=', 'r.expense_id')
            ->join('inventories as i', 'i.id', '=', 'r.inventory_id')
            ->where('r.status', 'Active')
            ->where('e.to_be_used', 'MBAO')
            ->when($companyId != 1, fn ($q) => $q->where('r.company_id', $companyId))
            ->when($storeId, fn ($q) => $q->where('i.store_id', $storeId));

        $dailyExpenses = DB::table('daily_expenses as d')
            ->where('d.status', 'Active')
            ->when($companyId != 1, fn ($q) => $q->where('d.company_id', $companyId))
            ->when($storeId, fn ($q) => $q->where('d.store_id', $storeId));

        $this->sales = $series($sales, 'invoice_date', 'amount_paid');
        $this->expensesChart = $series($inventoryExpenses, 'r.reg_at', 'r.amount_used');
        $this->dailyChart = $series($dailyExpenses, 'd.expense_date', 'd.amount');

        $this->emit('salesUpdated', [
            'categories' => $this->chartCategories,
            'sales' => $this->sales,
            'inventory' => $this->expensesChart,
            'daily' => $this->dailyChart,
        ]);
    }

    public function render()
    {
        return view('livewire.components.reports.summary');
    }
}
