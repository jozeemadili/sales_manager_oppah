<?php

namespace App\Http\Livewire\Components\Reports;

use App\Models\HotelInvoiceItem;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class SaleschartsHotel extends Component
{
    public function render()
    {
        $data = array();

        // $dbData = HotelInvoiceItem::select(DB::raw('month(created_at) as month'), DB::raw('sum(price * qty) as amount'),)->where(DB::raw('date(created_at)'), '>=', date('Y')."-01-01")->whereStatus('Paid')->groupBy('month')->get();
        $dbData = HotelInvoiceItem::select(
            DB::raw('MONTH(date_paid) as month'),
            DB::raw('SUM(DATEDIFF(end_date, start_date) * price) as amount')
        )
        ->where('status', 'Paid')
        ->whereDate('date_paid', '=', date('Y-m-d'))
        ->groupBy(DB::raw('MONTH(date_paid)'))
        ->get();
      
        for ($month = 0; $month <= 11; $month++) 
        {
            $results = $dbData->where('month', $month+1)->pluck('amount');
            if ($results->isNotEmpty()) 
            {
                $data[] = $results->first();
                // $data[] = $results->first() / 1000000;
            } else 
            {
                $data[] = 0;
            }
        }
        $this->sales = $data;
        return view('livewire.components.reports.salescharts-hotel');
    }
}
