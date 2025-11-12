<?php

namespace App\Http\Livewire\Components\Reports;

use App\Models\HotelCustomer;
use App\Models\HotelInvoiceItem;
use App\Models\Room;
use Livewire\Component;
use Carbon\Carbon;

class SummaryHotel extends Component
{
    public function render()
    {
        $HotelCustomer =  HotelCustomer::where('status', 'Active')->count();
        $Rooms =  Room::where('status', 'Active')->count();
        $RoomsOcupied =  Room::where('occupied', 'yes')->count();

        $items = HotelInvoiceItem::whereIn('status', ['Pending', 'Confermed'])->get();

        $Total_bill = 0;

        foreach ($items as $item) {
            $start = Carbon::parse($item->start_date);
            $end = Carbon::parse($item->end_date);
            $days = $end->diffInDays($start);
            $Total_bill += $days * $item->price;
        }

        $items_paid = HotelInvoiceItem::where('status', 'Paid')->whereDate('date_paid', Carbon::today())->get();

        $Total_bill_paid = 0;

        foreach ($items_paid as $item) {
            $start = Carbon::parse($item->start_date);
            $end = Carbon::parse($item->end_date);
            $days = $end->diffInDays($start);
            $Total_bill_paid += $days * $item->price;
        }
        // $sumProduct = Product::where('status', 'Active')
        //       ->sum(DB::raw('purchasing_price * qty'));
        //       $selling_price = Product::where('status', 'Active')
        //       ->sum(DB::raw('selling_price * qty'));
        //       $expcted_profit=$selling_price-$sumProduct;
            $Disbursed      = 0;  
            $Rejected       = 0;  
            $cannceled       = 0; 
            $allApplication        = 0;
        
   
            
        $this->summary = array('HotelCustomer' => number_format($HotelCustomer, 0, '.', ','), 'Rooms' => number_format($Rooms, 0, '.', ','), 'RoomsOcupied' => number_format($RoomsOcupied, 0, '.', ','), 'Total_bill' => number_format($Total_bill, 0, '.', ','), 'Total_bill_paid' => number_format($Total_bill_paid, 0, '.', ','), 'cannceled' => number_format($cannceled, 0, '.', ','));

        return view('livewire.components.reports.summary-hotel');
    }
}
