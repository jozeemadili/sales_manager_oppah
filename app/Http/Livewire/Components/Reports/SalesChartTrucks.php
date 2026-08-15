<?php

// namespace App\Http\Livewire\Components\Reports;

// use Livewire\Component;

// class SalesChartTrucks extends Component
// {
//     public function render()
//     {
//         return view('livewire.components.reports.sales-chart-trucks');
//     }
// }




namespace App\Http\Livewire\Components\Reports;

use Livewire\Component;
use App\Models\TrucksRoute;
use App\Models\OurTruck;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesChartTrucks extends Component
{
    public $chartData = [];

    public function render()
    {
        $companyId = Auth::user()->company_id;

        // Fetch all trucks
        $trucks = OurTruck::all();

        $series = [];

        foreach ($trucks as $truck) {

            // Query routes for each truck grouped by month
            $query = TrucksRoute::select(
                DB::raw('MONTH(route_date) as month'),
                DB::raw('SUM(total_fee -
                    (SELECT COALESCE(SUM(amount_tsh),0) FROM route_plans WHERE route_id = trucks_routes.id) -
                    (SELECT COALESCE(SUM(amount_used),0) FROM expenses_records_trucks WHERE route_id = trucks_routes.id)
                ) as balance')
            )
            ->where('truck_id', $truck->id)
            ->whereYear('route_date', date('Y'))
            ->groupBy(DB::raw('MONTH(route_date)'))
            ->orderBy(DB::raw('MONTH(route_date)'));

            if ($companyId != 1) {
                $query->where('company_id', $companyId);
            }

            $dbData = $query->get();

            // Build 12 month values for this truck
            $monthlyBalances = [];
            for ($month = 1; $month <= 12; $month++) {
                $value = $dbData->where('month', $month)->pluck('balance')->first();
                $monthlyBalances[] = $value ? (float)$value : 0;
            }

            // Add to series array
            $series[] = [
                'name' => $truck->plate_no,
                'data' => $monthlyBalances,
            ];
        }

        $this->chartData = $series;

        return view('livewire.components.reports.sales-chart-trucks');
    }
}


