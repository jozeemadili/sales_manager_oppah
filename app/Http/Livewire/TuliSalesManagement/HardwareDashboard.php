<?php

namespace App\Http\Livewire\TuliSalesManagement;

use Livewire\Component;
use App\Models\SalesTuli;
use App\Models\ProductsTuli;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class HardwareDashboard extends Component
{
    public $sales = [];

    public $summary = [];

    public function render()
    {
        $companyId = Auth::user()->company_id;

        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */

        $query = SalesTuli::query();

        if ($companyId != 1) {
            $query->where('company_id', $companyId);
        }
        $productQuery = ProductsTuli::query();
        /*
        |--------------------------------------------------------------------------
        | Summary Cards
        |--------------------------------------------------------------------------
        */

        $this->summary = [

            // Total Generated Amount Today
            'generated_amount' => (clone $query)
                ->whereDate('date_sold', today())
                ->sum(DB::raw('quantity * selling_price')),
        
        
            // Total Paid Amount Today
            // Completed sales today
            'paid_amount' => (clone $query)
            ->whereDate('date_sold', today())
            ->whereIn('status', [
                'sold_invoiced_completed',
                'sold',
                'sold_discounted'
            ])
            ->sum(DB::raw('quantity * selling_price')),
        
        
            // Remaining Unpaid Today
            // Partial + Pending today
            'unpaid_amount' => (clone $query)
            ->whereDate('date_sold', today())
            ->whereNotIn('status', [
                'sold_invoiced_completed',
                'sold',
                'sold_discounted'
            ])
            ->sum(DB::raw('quantity * selling_price')),
        
        
            // Total Unpaid All Time
            'unpaid_overall' => (clone $query)
            ->whereNotIn('status', [
                'sold_invoiced_completed',
                'sold',
                'sold_discounted'
            ])
            ->sum(DB::raw('quantity * selling_price')),

                // Current Stock Value
            'sumProduct' => (clone $productQuery)
    ->sum(DB::raw('qty_remained * purchasing_price')),
        
        ];

        /*
        |--------------------------------------------------------------------------
        | Monthly Sales Chart
        |--------------------------------------------------------------------------
        */

        $chart = SalesTuli::select(
                DB::raw('MONTH(date_sold) as month'),
                DB::raw('SUM(quantity * selling_price) as total_sales')
            )
            ->whereYear('date_sold', now()->year);

        if ($companyId != 1) {
            $chart->where('company_id', $companyId);
        }

        $chart = $chart
            ->groupBy(DB::raw('MONTH(date_sold)'))
            ->orderBy(DB::raw('MONTH(date_sold)'))
            ->get();

        $this->sales = [];

        for ($month = 1; $month <= 12; $month++) {

            $record = $chart->where('month', $month)->first();

            $this->sales[] = $record
                ? (float) $record->total_sales
                : 0;
        }

        return view('livewire.tuli-sales-management.hardware-dashboard');
    }
}