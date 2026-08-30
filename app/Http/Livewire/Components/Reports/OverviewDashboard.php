<?php

namespace App\Http\Livewire\Components\Reports;

use App\Models\BankDeposist;
use App\Models\Customer;
use App\Models\CustomersTuli;
use App\Models\Invoice;
use App\Models\InvoicePaymentDetail;
use App\Models\OurTruck;
use App\Models\Product;
use App\Models\ProductsTuli;
use App\Models\SalesTuli;
use App\Models\TrucksRoute;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Combines the three existing per-module dashboards (Mbao, Hardware, Trucks)
 * into a single overview. Deliberately read-only and additive: it re-derives
 * its numbers with the same queries/conventions as
 * Components\Reports\Summary (Mbao) and TuliSalesManagement\HardwareDashboard
 * (Hardware) rather than reusing those classes directly, since neither is
 * built to hand back its numbers without also rendering its own view.
 */
class OverviewDashboard extends Component
{
    public array $mbao = [];
    public array $hardware = [];
    public array $trucks = [];

    public array $mbaoSales = [];
    public array $hardwareSales = [];
    public array $truckBalance = [];

    public function mount()
    {
        $this->loadMbao();
        $this->loadHardware();
        $this->loadTrucks();
    }

    private function companyId(): int
    {
        return (int) Auth::user()->company_id;
    }

    private function scopeByCompany($query, string $column = 'company_id')
    {
        if ($this->companyId() !== 1) {
            $query->where($column, $this->companyId());
        }

        return $query;
    }

    private function monthlySeries($rows, string $valueKey): array
    {
        $series = [];
        for ($month = 1; $month <= 12; $month++) {
            $row = $rows->firstWhere('month', $month);
            $series[] = $row ? (float) $row->{$valueKey} : 0.0;
        }

        return $series;
    }

    private function loadMbao(): void
    {
        $invoiceQuery = $this->scopeByCompany(Invoice::query());

        $today = [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()];
        $invoiceTodayQuery = (clone $invoiceQuery)->whereBetween('invoice_date', $today);

        $paidToday = $this->scopeByCompany(InvoicePaymentDetail::whereDate('date_payed', Carbon::today()))
            ->sum('amount_submitted');

        $stockValue = $this->scopeByCompany(Product::where('status', 'Active'))
            ->sum(DB::raw('selling_price * qty_remained'));

        $customers = $this->scopeByCompany(Customer::query());

        $this->mbao = [
            'stock_value'     => $stockValue,
            'generated_today' => $invoiceTodayQuery->sum('total_invoice_amount'),
            'paid_today'      => $paidToday,
            'unpaid_today'    => $invoiceTodayQuery->sum('amount_remained'),
            'unpaid_overall'  => $invoiceQuery->sum('amount_remained'),
            'customers'       => $customers->count(),
        ];

        $rows = $this->scopeByCompany(
            Invoice::select(DB::raw('MONTH(invoice_date) as month'), DB::raw('SUM(amount_paid) as amount'))
                ->whereYear('invoice_date', now()->year)
        )->groupBy(DB::raw('MONTH(invoice_date)'))->get();

        $this->mbaoSales = $this->monthlySeries($rows, 'amount');
    }

    private function loadHardware(): void
    {
        $salesQuery = $this->scopeByCompany(SalesTuli::query());
        $completed = ['sold_invoiced_completed', 'sold', 'sold_discounted'];

        $stockValue = $this->scopeByCompany(ProductsTuli::query())
            ->sum(DB::raw('qty_remained * purchasing_price'));

        $customers = $this->scopeByCompany(CustomersTuli::query());

        $this->hardware = [
            'stock_value'     => $stockValue,
            'generated_today' => (clone $salesQuery)->whereDate('date_sold', today())
                ->sum(DB::raw('quantity * selling_price')),
            'paid_today'      => (clone $salesQuery)->whereDate('date_sold', today())
                ->whereIn('status', $completed)
                ->sum(DB::raw('quantity * selling_price')),
            'unpaid_today'    => (clone $salesQuery)->whereDate('date_sold', today())
                ->whereNotIn('status', $completed)
                ->sum(DB::raw('quantity * selling_price')),
            'unpaid_overall'  => (clone $salesQuery)->whereNotIn('status', $completed)
                ->sum(DB::raw('quantity * selling_price')),
            'customers'       => $customers->count(),
        ];

        $rows = $this->scopeByCompany(
            SalesTuli::select(DB::raw('MONTH(date_sold) as month'), DB::raw('SUM(quantity * selling_price) as amount'))
                ->whereYear('date_sold', now()->year)
        )->groupBy(DB::raw('MONTH(date_sold)'))->get();

        $this->hardwareSales = $this->monthlySeries($rows, 'amount');
    }

    private function loadTrucks(): void
    {
        $fleetSize = OurTruck::count();

        $routeQuery = $this->scopeByCompany(TrucksRoute::query());

        $tripsThisMonth = (clone $routeQuery)->whereMonth('route_date', now()->month)
            ->whereYear('route_date', now()->year)
            ->count();

        $revenueToday = (clone $routeQuery)->whereDate('route_date', today())->sum('total_fee');

        $depositsThisMonth = BankDeposist::whereMonth('deposited_date', now()->month)
            ->whereYear('deposited_date', now()->year)
            ->sum('deposited_amount');

        // Net balance = trip fees minus route plan spend minus recorded expenses,
        // mirrors Components\Reports\SalesChartTrucksCombined's per-truck formula
        // but summed across the whole fleet instead of split per plate.
        $balanceExpr = DB::raw('SUM(total_fee -
                (SELECT COALESCE(SUM(amount_tsh),0) FROM route_plans WHERE route_id = trucks_routes.id) -
                (SELECT COALESCE(SUM(amount_used),0) FROM expenses_records_trucks WHERE route_id = trucks_routes.id)
            ) as balance');

        $balanceThisMonth = (clone $routeQuery)
            ->whereMonth('route_date', now()->month)
            ->whereYear('route_date', now()->year)
            ->select($balanceExpr)
            ->value('balance') ?? 0;

        $this->trucks = [
            'fleet_size'          => $fleetSize,
            'trips_this_month'    => $tripsThisMonth,
            'revenue_today'       => $revenueToday,
            'balance_this_month'  => $balanceThisMonth,
            'deposits_this_month' => $depositsThisMonth,
        ];

        $rows = $this->scopeByCompany(
            TrucksRoute::select(DB::raw('MONTH(route_date) as month'), $balanceExpr)
                ->whereYear('route_date', now()->year)
        )->groupBy(DB::raw('MONTH(route_date)'))->get();

        $this->truckBalance = $this->monthlySeries($rows, 'balance');
    }

    public function render()
    {
        return view('livewire.components.reports.overview-dashboard');
    }
}
