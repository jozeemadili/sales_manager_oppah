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
use App\Http\Livewire\SalesManagement\QuickSale;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Summary extends Component
{
    public $summary = [];
    public $sales = [];
    public $storeId = 'all';
    public $expensesChart = [];
    public $dailyBreakdown = [];
    public $dailyChart = [];
    public $chartPeriod = 'year';
    public $chartCategories = [];
    public $chartStoreName = '';
    public $balanceChart = [];
    public $chartTotals = [];

    // Drill-down list behind the invoice cards
    public $detailType = null;
    public $detailTitle = '';
    public $detailRows = [];
    public $detailCount = 0;
    public $detailTotal = 0;
    public $detailByStatus = [];

    const DETAIL_LIMIT = 500;


    public function mount()
    {
        $this->loadData();
    }

    // The Mbao dashboard is fixed to the main (Mzinga) store; re-resolved on
    // every load so the public property can't be pointed elsewhere.
    private function useMainStore()
    {
        $store = Store::mainStore();
        $this->storeId = $store ? $store->id : 'all';
        $this->chartStoreName = $store ? $store->name : 'All Stores';
    }

    private function loadData()
    {
        $this->useMainStore();
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

        // Today only: the table empties itself when the day changes.
        $this->dailyBreakdown = (clone $dailyQuery)
            ->where('d.expense_date', Carbon::today()->toDateString())
            ->groupBy('t.name')
            ->orderByDesc('total')
            ->get(['t.name as name', DB::raw('SUM(d.amount) as total'), DB::raw('COUNT(*) as entries')])
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

        // Quick (cash) sales have no invoice but count as generated and paid.
        $quickToday = (float) $this->quickSales()
            ->whereBetween('s.date_sold', [$startTime, $endTime])
            ->sum(DB::raw('s.selling_price * s.quantity'));

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
            'generated_amount' => number_format($invoiceTodayQuery->sum('total_invoice_amount') + $quickToday, 2, '.', ','),
            'paid_amount'      => number_format($sumToday + $quickToday, 2, '.', ','),
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
        $this->useMainStore();
        $companyId = Auth::user()->company_id;
        $storeId = $this->storeId !== 'all' ? $this->storeId : null;

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

        $invoiceSales = $series($sales, 'invoice_date', 'amount_paid');
        $quickSales = $series($this->quickSales(), 's.date_sold', 's.selling_price * s.quantity');
        $this->sales = array_map(fn ($a, $b) => $a + $b, $invoiceSales, $quickSales);
        $this->expensesChart = $series($inventoryExpenses, 'r.reg_at', 'r.amount_used');
        $this->dailyChart = $series($dailyExpenses, 'd.expense_date', 'd.amount');

        // Balance = Sales - (Inventory Expenses + Daily Expenses), per bar and for the period.
        $this->balanceChart = array_map(
            fn ($sale, $inventory, $daily) => $sale - ($inventory + $daily),
            $this->sales, $this->expensesChart, $this->dailyChart
        );

        $this->chartTotals = [
            'sales' => array_sum($this->sales),
            'inventory' => array_sum($this->expensesChart),
            'daily' => array_sum($this->dailyChart),
            'balance' => array_sum($this->balanceChart),
        ];

        $this->emit('salesUpdated', [
            'balance' => $this->balanceChart,
            'categories' => $this->chartCategories,
            'sales' => $this->sales,
            'inventory' => $this->expensesChart,
            'daily' => $this->dailyChart,
        ]);
    }

    /**
     * Completed Mbao quick (cash) sales: no invoice is created for them, so
     * they are added to the invoice-based figures separately. Amount is
     * selling_price x quantity; always fully paid.
     */
    private function quickSales()
    {
        $companyId = Auth::user()->company_id;

        return DB::table('sales as s')
            ->join('products as p', 'p.id', '=', 's.product_id')
            ->where('s.customer_id', QuickSale::WALK_IN_CUSTOMER)
            ->whereIn('s.status', QuickSale::SOLD_STATUSES)
            ->when($companyId != 1, fn ($q) => $q->where('s.company_id', $companyId))
            ->when($this->storeId !== 'all', fn ($q) => $q->where('p.store_id', $this->storeId));
    }

    // One quick-sale transaction = the items one cashier completed in one go.
    private function quickSaleTransactionsToday()
    {
        return $this->quickSales()
            ->leftJoin('users as u', 'u.id', '=', 's.sold_by')
            ->whereBetween('s.date_sold', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])
            ->groupBy('s.sold_by', 's.date_sold', 'u.first_name')
            ->orderBy('s.date_sold')
            ->get([
                's.sold_by', 's.date_sold', 'u.first_name',
                DB::raw('MIN(s.id) as first_id'),
                DB::raw('COUNT(*) as items'),
                DB::raw('SUM(s.selling_price * s.quantity) as total'),
            ]);
    }

    /**
     * List the invoices / payments behind one of the invoice cards.
     * Uses the same scope as the cards: Mzinga store, user's company.
     */
    public function showDetail($type)
    {
        $this->useMainStore();
        $companyId = Auth::user()->company_id;
        $today = [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()];

        if ($type === 'paid_today') {
            $query = InvoicePaymentDetail::with('invoice.customer')
                ->whereDate('date_payed', Carbon::today())
                ->when($this->storeId !== 'all', fn ($q) => $q->whereHas('invoice.customer', fn ($c) => $c->where('store_id', $this->storeId)));

            $quick = $this->quickSaleTransactionsToday();

            $this->detailTitle = 'Payments Received Today';
            $this->detailCount = (clone $query)->count() + $quick->count();
            $this->detailTotal = (float) (clone $query)->sum('amount_submitted') + (float) $quick->sum('total');
            $this->detailByStatus = [];
            $this->detailRows = $query->orderByDesc('date_payed')->limit(self::DETAIL_LIMIT)->get()
                ->map(fn ($p) => [
                    'receipt' => $p->receipt_number,
                    'invoice_id' => $p->invoice_no,
                    'customer' => optional(optional($p->invoice)->customer)->name,
                    'amount' => (float) $p->amount_submitted,
                    'time' => Carbon::parse($p->date_payed)->format('H:i'),
                    'sort' => Carbon::parse($p->date_payed)->timestamp,
                    'by' => $p->payer_id,
                    'channel' => $p->channel,
                ])
                ->concat($quick->map(fn ($q) => [
                    'receipt' => 'QUICK-'.$q->first_id,
                    'invoice_id' => null,
                    'customer' => 'Walk-in ('.$q->items.' '.($q->items == 1 ? 'item' : 'items').')',
                    'amount' => (float) $q->total,
                    'time' => Carbon::parse($q->date_sold)->format('H:i'),
                    'sort' => Carbon::parse($q->date_sold)->timestamp,
                    'by' => $q->first_name,
                    'channel' => 'Quick Sale',
                ]))
                ->sortByDesc('sort')->values()->all();
        } else {
            $query = Invoice::with('customer')
                ->when($companyId != 1, fn ($q) => $q->where('company_id', $companyId))
                ->when($this->storeId !== 'all', fn ($q) => $q->whereHas('customer', fn (Builder $c) => $c->where('store_id', $this->storeId)));

            if ($type === 'unpaid_all') {
                $query->where('amount_remained', '>', 0);
                $this->detailTitle = 'Unpaid Invoices (All Time)';
                $sumColumn = 'amount_remained';
            } elseif ($type === 'unpaid_today') {
                $query->whereBetween('invoice_date', $today)->where('amount_remained', '>', 0);
                $this->detailTitle = 'Unpaid Invoices (Today)';
                $sumColumn = 'amount_remained';
            } else {
                $type = 'generated_today';
                $query->whereBetween('invoice_date', $today);
                $this->detailTitle = 'Invoices Generated Today';
                $sumColumn = 'total_invoice_amount';
            }

            $this->detailCount = (clone $query)->count();
            $this->detailTotal = (float) (clone $query)->sum($sumColumn);
            $this->detailByStatus = (clone $query)->toBase()
                ->groupBy('status')
                ->get(['status', DB::raw('COUNT(*) as n'), DB::raw("SUM($sumColumn) as amount")])
                ->map(fn ($r) => ['status' => $r->status, 'n' => (int) $r->n, 'amount' => (float) $r->amount])
                ->sortByDesc('amount')->values()->all();
            $this->detailRows = $query->orderBy('invoice_date')->limit(self::DETAIL_LIMIT)->get()
                ->map(fn ($i) => [
                    'id' => $i->id,
                    'date' => optional($i->invoice_date)->format('d/m/Y H:i'),
                    'age' => $i->invoice_date ? Carbon::parse($i->invoice_date)->startOfDay()->diffInDays(Carbon::today()) : null,
                    'customer' => optional($i->customer)->name,
                    'status' => $i->status,
                    'total' => (float) $i->total_invoice_amount,
                    'paid' => (float) $i->amount_paid,
                    'remained' => (float) $i->amount_remained,
                ])->all();

            // Quick (cash) sales count as generated today too.
            if ($type === 'generated_today') {
                $quick = $this->quickSaleTransactionsToday();

                if ($quick->isNotEmpty()) {
                    $this->detailCount += $quick->count();
                    $this->detailTotal += (float) $quick->sum('total');
                    $this->detailByStatus[] = ['status' => 'Quick Sale', 'n' => $quick->count(), 'amount' => (float) $quick->sum('total')];
                    $this->detailRows = array_merge($this->detailRows, $quick->map(fn ($q) => [
                        'id' => null,
                        'date' => Carbon::parse($q->date_sold)->format('d/m/Y H:i'),
                        'age' => 0,
                        'customer' => 'Walk-in ('.$q->items.' '.($q->items == 1 ? 'item' : 'items').', by '.$q->first_name.')',
                        'status' => 'Quick Sale',
                        'total' => (float) $q->total,
                        'paid' => (float) $q->total,
                        'remained' => 0,
                    ])->all());
                }
            }
        }

        $this->detailType = $type;
        $this->dispatchBrowserEvent('open-summary-detail');
    }

    public function render()
    {
        return view('livewire.components.reports.summary');
    }
}
