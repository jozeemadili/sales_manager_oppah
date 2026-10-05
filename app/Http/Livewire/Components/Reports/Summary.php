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
use Livewire\WithFileUploads;
use App\Models\BankDeposist;
use App\Models\BankDepositFile;
use App\Models\DayClosure;
use App\Models\DailyExpense;
use App\Http\Livewire\SalesManagement\QuickSale;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Summary extends Component
{
    use WithFileUploads;

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
    public $detailCustomers = [];

    const DETAIL_LIMIT = 500;

    // Bank deposit of today's balance
    public $depositAmount = 0;
    public $depositBank = '';
    public $depositAccount = '';
    public $depositSlips = [];
    public $depositSuggestions = [];
    public $depositList = [];

    // End Day
    public $endDayPreview = [];
    public $dayLock = null;


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
        $this->loadDayLock();
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

        $balanceToday = ($sumToday + $quickToday) - ($expensesToday + $dailyToday);
        $depositedToday = $this->depositedToday();
        $toDeposit = max(0, round($balanceToday - $depositedToday, 2));

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
            // Balance (Today) = money received today - expenses recorded today.
            'balance_today_raw' => $balanceToday,
            'balance_today'    => number_format($balanceToday, 2, '.', ','),
            'deposited_today'  => number_format($depositedToday, 2, '.', ','),
            'to_deposit_raw'   => $toDeposit,
            'to_deposit'       => number_format($toDeposit, 2, '.', ','),
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

    private function loadDayLock()
    {
        $lock = DayClosure::activeLock($this->storeId !== 'all' ? $this->storeId : null);

        $this->dayLock = $lock ? [
            'id' => $lock->id,
            'closed_at' => $lock->closed_at->format('d M Y H:i'),
            'locked_until' => $lock->locked_until->format('d M Y H:i'),
            'by' => optional($lock->closer)->first_name,
        ] : null;
    }

    /**
     * Everything shown on the End Day preview and PDF, as plain arrays so it
     * can be stored with the closing and re-printed later unchanged.
     */
    private function endDaySnapshot()
    {
        $this->loadData();
        $today = Carbon::today();

        $lists = [];
        foreach (['generated_today', 'paid_today', 'unpaid_today', 'unpaid_all'] as $type) {
            $this->showDetail($type, false);
            $lists[$type] = [
                'count' => $this->detailCount,
                'total' => $this->detailTotal,
                'rows' => $this->detailRows,
                'customers' => $this->detailCustomers,
            ];
        }
        $this->detailType = null;

        $dailyEntries = DailyExpense::active()
            ->with(['type', 'user'])
            ->where('store_id', $this->storeId)
            ->whereDate('expense_date', $today)
            ->orderBy('created_at')
            ->get()
            ->map(fn ($e) => [
                'type' => optional($e->type)->name,
                'description' => $e->description,
                'amount' => (float) $e->amount,
                'by' => optional($e->user)->first_name,
                'time' => Carbon::parse($e->created_at)->format('H:i'),
            ])->all();

        $deposits = $this->mbaoDeposits()
            ->with(['files', 'user'])
            ->whereDate('balance_date', $today)
            ->orderBy('id')
            ->get()
            ->map(fn ($d) => [
                'time' => optional($d->deposited_date)->format('H:i'),
                'bank' => $d->bank_name,
                'account' => $d->account_number,
                'amount' => (float) $d->deposited_amount,
                'by' => optional($d->user)->first_name,
                'slips' => $d->files->map(fn ($f) => ['id' => $f->id, 'path' => $f->file_path, 'image' => $f->isImage()])->all(),
            ])->all();

        $num = fn ($v) => (float) str_replace(',', '', $v);
        $ledger = $this->timberLedger();

        // Bottom part of the paper ledger.
        $paidToday = $num($this->summary['paid_amount']);
        $ledger['summary'] = [
            'mauzo' => $ledger['totals']['sales_total'],
            'matumizi_ndani' => $num($this->summary['daily_today']),
            'matumizi_nje' => $num($this->summary['expenses_today']),
            'matumizi_jumla' => $num($this->summary['daily_today']) + $num($this->summary['expenses_today']),
            'madeni_yaliyolipwa' => max(0, $paidToday - $ledger['quick_sales_total']),
            'madeni_yasiyolipwa' => $num($this->summary['unpaid_amount']),
            'kilichobaki' => (float) $this->summary['balance_today_raw'],
            'bank_jumla' => $num($this->summary['deposited_today']),
            'bank_accounts' => collect($deposits)->map(fn ($d) => trim($d['account'].' ('.strtoupper($d['bank']).')'))->unique()->values()->all(),
        ];

        return [
            'store' => $this->chartStoreName,
            'business_date' => $today->toDateString(),
            'generated_at' => Carbon::now()->format('d M Y H:i'),
            'stock_selling' => $num($this->summary['sumProduct']),
            'stock_cost' => $num($this->summary['sumProductCost']),
            'generated_today' => $num($this->summary['generated_amount']),
            'paid_today' => $num($this->summary['paid_amount']),
            'unpaid_today' => $num($this->summary['unpaid_amount']),
            'unpaid_all' => $num($this->summary['unpaid_overall']),
            'inventory_expenses_today' => $num($this->summary['expenses_today']),
            'daily_expenses_today' => $num($this->summary['daily_today']),
            'balance_today' => (float) $this->summary['balance_today_raw'],
            'deposited_today' => $num($this->summary['deposited_today']),
            'left_to_deposit' => (float) $this->summary['to_deposit_raw'],
            'lists' => $lists,
            'ledger' => $ledger,
            'daily_by_type' => $this->dailyBreakdown,
            'daily_entries' => $dailyEntries,
            'deposits' => $deposits,
        ];
    }

    /**
     * Timber Control Ledger (the paper "Timber Control Ledger" form), one row
     * per timber type (product name) for the main store, for today:
     *  - Zilizouzwa: pieces sold today (confirmed invoices + quick sales)
     *  - Zilizobaki: pieces in stock now
     *  - Zilizosalia: opening = sold + remaining (as on the paper form)
     *  - Jumla ya bei: sold x cost price, and the actual sales amount
     */
    private function timberLedger()
    {
        $today = [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()];
        $companyId = Auth::user()->company_id;
        $key = fn ($name) => strtoupper(preg_replace('/\s+/', ' ', trim($name)));
        $rows = [];
        $row = function ($name) use (&$rows, $key) {
            $k = $key($name);
            return $rows[$k] ??= ['type' => $k, 'sold' => 0, 'remaining' => 0, 'cost_total' => 0, 'sales_total' => 0, 'unit_cost' => 0, 'unit_price' => 0];
        };

        // Stock now, with the latest prices as the per-piece fallback.
        $stock = Product::where('status', 'Active')
            ->where('store_id', $this->storeId)
            ->when($companyId != 1, fn ($q) => $q->where('company_id', $companyId))
            ->orderBy('id')
            ->get(['product_name', 'qty_remained', 'purchasing_price', 'selling_price']);

        foreach ($stock as $p) {
            $r = $row($p->product_name);
            $r['remaining'] += (float) $p->qty_remained;
            $r['unit_cost'] = (float) $p->purchasing_price ?: $r['unit_cost'];
            $r['unit_price'] = (float) $p->selling_price ?: $r['unit_price'];
            $rows[$r['type']] = $r;
        }

        // Sold today on confirmed invoices (confirming sets invoice_date and deducts stock).
        $invoiceSold = DB::table('invoice_items as it')
            ->join('invoices as i', 'i.id', '=', 'it.invoice_id')
            ->join('products as p', 'p.id', '=', 'it.product_id')
            ->whereIn('i.status', ['Confirmed', 'Partial_Paid', 'Paid'])
            ->whereBetween('i.invoice_date', $today)
            ->where('p.store_id', $this->storeId)
            ->get(['p.product_name', 'it.qty', 'it.price', 'p.purchasing_price']);

        $quickSold = $this->quickSales()
            ->whereBetween('s.date_sold', $today)
            ->get(['p.product_name', 's.quantity as qty', 's.selling_price as price', 'p.purchasing_price']);

        foreach ($invoiceSold->concat($quickSold) as $line) {
            $r = $row($line->product_name);
            $r['sold'] += (float) $line->qty;
            $r['cost_total'] += (float) $line->qty * (float) $line->purchasing_price;
            $r['sales_total'] += (float) $line->qty * (float) $line->price;
            $rows[$r['type']] = $r;
        }

        $rows = collect($rows)
            ->filter(fn ($r) => $r['sold'] > 0 || $r['remaining'] > 0)
            ->map(function ($r) {
                $r['opening'] = $r['sold'] + $r['remaining'];
                if ($r['sold'] > 0) {
                    $r['unit_cost'] = $r['cost_total'] / $r['sold'] ?: $r['unit_cost'];
                    $r['unit_price'] = $r['sales_total'] / $r['sold'];
                }
                return $r;
            })
            ->sortBy('type', SORT_NATURAL)
            ->values()->all();

        $sum = fn ($col) => array_sum(array_column($rows, $col));

        return [
            'rows' => $rows,
            'totals' => [
                'opening' => $sum('opening'),
                'sold' => $sum('sold'),
                'remaining' => $sum('remaining'),
                'cost_total' => $sum('cost_total'),
                'sales_total' => $sum('sales_total'),
            ],
            'quick_sales_total' => (float) $quickSold->sum(fn ($l) => $l->qty * $l->price),
        ];
    }

    public function previewEndDay()
    {
        $this->loadDayLock();

        if ($this->dayLock) {
            $this->dispatchBrowserEvent('end-day-message', ['text' => 'This day is already closed.']);
            return;
        }

        $this->endDayPreview = $this->endDaySnapshot();
        $this->dispatchBrowserEvent('open-end-day-modal');
    }

    public function confirmEndDay()
    {
        $this->loadDayLock();

        if ($this->dayLock || $this->storeId === 'all') {
            $this->dispatchBrowserEvent('end-day-message', ['text' => 'This day is already closed.']);
            return;
        }

        // Re-read the figures at the moment of closing.
        $snapshot = $this->endDaySnapshot();
        $snapshot['closed_by'] = Auth::user()->first_name;

        $closure = DayClosure::create([
            'store_id' => $this->storeId,
            'business_date' => $snapshot['business_date'],
            'closed_by' => Auth::id(),
            'closed_at' => Carbon::now(),
            'locked_until' => DayClosure::lockUntil(Carbon::now()),
            'status' => 'Closed',
            'snapshot' => $snapshot,
        ]);

        $this->endDayPreview = [];
        $this->loadData();
        $this->dispatchBrowserEvent('end-day-closed', ['pdf' => route('day-closure-pdf', $closure->id)]);
    }

    // Admins only: undo an End Day pressed by mistake.
    public function reopenDay()
    {
        abort_unless(Auth::user()->hasFullAccess(), 403);

        $lock = DayClosure::activeLock($this->storeId !== 'all' ? $this->storeId : null);

        if ($lock) {
            $lock->update(['status' => 'Reopened', 'reopened_by' => Auth::id(), 'reopened_at' => Carbon::now()]);
        }

        $this->loadData();
    }

    private function mbaoDeposits()
    {
        return BankDeposist::where('source', BankDeposist::SOURCE_MBAO)
            ->when($this->storeId !== 'all', fn ($q) => $q->where('store_id', $this->storeId));
    }

    private function depositedToday()
    {
        return (float) $this->mbaoDeposits()
            ->whereDate('balance_date', Carbon::today())
            ->sum('deposited_amount');
    }

    public function openDeposit()
    {
        $this->loadData();
        $this->resetErrorBag();
        $this->depositSlips = [];
        $this->depositAmount = $this->summary['to_deposit_raw'];

        // Suggest the bank/account used last time.
        $this->depositSuggestions = $this->mbaoDeposits()
            ->whereNotNull('account_number')
            ->orderByDesc('id')
            ->get(['bank_name', 'account_number'])
            ->unique(fn ($d) => $d->bank_name.'|'.$d->account_number)
            ->take(10)
            ->map(fn ($d) => ['bank' => $d->bank_name, 'account' => $d->account_number])
            ->values()->all();

        if ($this->depositSuggestions && !$this->depositBank) {
            $this->depositBank = $this->depositSuggestions[0]['bank'];
            $this->depositAccount = $this->depositSuggestions[0]['account'];
        }

        $this->dispatchBrowserEvent('open-deposit-modal');
    }

    public function saveDeposit()
    {
        // Re-read what is left to deposit right now (another deposit may have been saved meanwhile).
        $this->loadData();
        $left = (float) $this->summary['to_deposit_raw'];

        if ($left <= 0) {
            $this->addError('depositAmount', 'There is no balance left to deposit today.');
            return;
        }

        $this->depositAmount = str_replace(',', '', (string) $this->depositAmount);

        $this->validate([
            'depositAmount' => ['required', 'numeric', 'gt:0', 'max:'.$left],
            'depositBank' => ['required', 'string', 'max:100'],
            'depositAccount' => ['required', 'string', 'max:100'],
            'depositSlips' => ['required', 'array', 'min:1'],
            'depositSlips.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'depositAmount.gt' => 'Enter an amount above 0.',
            'depositAmount.max' => 'You can deposit at most '.number_format($left, 2).' TZS (left to deposit today).',
            'depositSlips.required' => 'Attach at least one bank slip.',
            'depositSlips.*.mimes' => 'Slips must be JPG, PNG or PDF.',
            'depositSlips.*.max' => 'Each slip must be 5 MB or smaller.',
        ]);

        // Part of the balance can be deposited now and the rest later today.
        $amount = round((float) $this->depositAmount, 2);

        $deposit = BankDeposist::create([
            'bank_name' => trim($this->depositBank),
            'account_number' => trim($this->depositAccount),
            'deposited_amount' => $amount,
            'deposited_by' => Auth::id(),
            'status' => 'Pending',
            'deposited_date' => Carbon::now(),
            'deposit_origin' => 'Mbao daily balance '.Carbon::today()->format('d/m/Y'),
            'source' => BankDeposist::SOURCE_MBAO,
            'store_id' => $this->storeId !== 'all' ? $this->storeId : null,
            'balance_date' => Carbon::today()->toDateString(),
            'created_at' => Carbon::now(),
        ]);

        foreach ($this->depositSlips as $slip) {
            $deposit->files()->create(['file_path' => $slip->store('bank_deposits', 'public')]);
        }

        $this->depositSlips = [];
        $this->loadData();
        $this->dispatchBrowserEvent('deposit-saved', [
            'amount' => number_format($amount, 2),
            'left' => $this->summary['to_deposit'],
        ]);
    }

    public function showDeposits()
    {
        $this->useMainStore();

        $this->depositList = $this->mbaoDeposits()
            ->with(['files', 'user'])
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            ->map(fn ($d) => [
                'date' => optional($d->deposited_date)->format('d/m/Y H:i'),
                'balance_date' => optional($d->balance_date)->format('d/m/Y'),
                'amount' => (float) $d->deposited_amount,
                'bank' => $d->bank_name,
                'account' => $d->account_number,
                'by' => optional($d->user)->first_name,
                'status' => $d->status,
                'slips' => $d->files->map(fn (BankDepositFile $f) => [
                    'url' => $f->file_url,
                    'image' => $f->isImage(),
                ])->all(),
            ])->all();

        $this->dispatchBrowserEvent('open-deposits-list');
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
    public function showDetail($type, $open = true)
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
            $this->detailCustomers = [];
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

            // Unpaid (all time) is shown per customer, each linking to a statement.
            $this->detailCustomers = $type !== 'unpaid_all' ? [] : (clone $query)->toBase()
                ->join('customers as c', 'c.id', '=', 'invoices.customer_id')
                ->groupBy('invoices.customer_id', 'c.name', 'c.phone')
                ->orderByDesc(DB::raw('SUM(invoices.amount_remained)'))
                ->get([
                    'invoices.customer_id', 'c.name', 'c.phone',
                    DB::raw('COUNT(*) as invoices'),
                    DB::raw('SUM(invoices.total_invoice_amount) as total'),
                    DB::raw('SUM(invoices.amount_paid) as paid'),
                    DB::raw('SUM(invoices.amount_remained) as remained'),
                    DB::raw('MIN(invoices.invoice_date) as oldest'),
                ])
                ->map(fn ($r) => [
                    'id' => $r->customer_id,
                    'name' => $r->name,
                    'phone' => $r->phone,
                    'invoices' => (int) $r->invoices,
                    'total' => (float) $r->total,
                    'paid' => (float) $r->paid,
                    'remained' => (float) $r->remained,
                    'oldest_days' => $r->oldest ? Carbon::parse($r->oldest)->startOfDay()->diffInDays(Carbon::today()) : null,
                ])->all();
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

        if ($open) {
            $this->dispatchBrowserEvent('open-summary-detail');
        }
    }

    public function render()
    {
        return view('livewire.components.reports.summary');
    }
}
