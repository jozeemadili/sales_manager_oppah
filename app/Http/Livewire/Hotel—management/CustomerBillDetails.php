<?php

namespace App\Http\Livewire\Hotel—management;

use App\Models\HotelInvoice;
use App\Models\HotelInvoiceItem;
use App\Models\Product;
use App\Models\Room;
use App\Models\RoomBooking;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\returnArgument;

class CustomerBillDetails extends Component
{
    public $Customers_details;
    public $customerquery;
    public $customer;
    public $startDate;
    public $endDate;
    public $selectedCustomer;
    public $customers = [];

    public function mount()
        {
            
            if($this->customer != null)
            {
                $this->customerquery = $this->customer->product_name ." | ". $this->customer->barcode;
            
            }
        }
    public function render()
    {
        $RoomBooking=RoomBooking::where('customer_id',$this->Customers_details->id)->where('status','Pending')->get();
        return view('livewire.hotel—management.customer-bill-details',['bookings'=>$RoomBooking]);
    }
    public function selectCustomer($customer)
    {
        $this->selectedCustomer = $customer;
        // dd($this->startDate."--".$this->endDate);
        if(empty($this->startDate) || empty($this->endDate))
        {
            session()->flash('error', 'Please Remembert to Choosed End date  start date.');
            return;
        }
        $startDate = Carbon::parse($this->startDate);  // Ensure start_date is a Carbon instance
        $endDate = Carbon::parse($this->endDate);
        

        if ($endDate->lte($startDate)) {
            session()->flash('error', 'End date cannot be Less than the start date.');
        }else
        {
            $item = RoomBooking::where('customer_id', $this->Customers_details->id)->where('room_id',$customer['id'])->where('status','Pending')->where('start_date',$this->startDate)->where('end_date',$this->endDate)->first();
            if (!is_null($item)) {
                session()->flash('error', 'The details you are trying to add are either duplicates or have already been billed..');
                return;
            }
            else
            {
                $conflict = RoomBooking::where('room_id',$customer['id'])->where(function ($query) {
                    $query->whereDate('start_date', '<=', $this->endDate)
                          ->whereDate('end_date', '>=', $this->startDate);
                })->exists();
                if ($conflict) {
                    session()->flash('error', 'Selected dates are not available. Please choose different dates.');
                }else 
                {
                    // dd("test ".$customer['id']."");
                    $user = RoomBooking::create(
                        [
                            'customer_id'                => intval($this->Customers_details->id),
                            'room_id'                    => $customer['id'],
                            'start_date'                 => $this->startDate,
                            'end_date'                   => $this->endDate,
                            'price'                      => 0,
                            'sold_by'                    => intval(Auth::user()->id),
                            'status'                     => "Pending",
                            'date_sold'                  => date('Y-m-d H:i:s'),
                            'company_id'                 => intval(Auth::user()->company_id),
                            
                        ]);

                        $room = Room::find($customer['id']);

                        if ($room) {
                            // Convert end_date to Carbon and add one day
                            $room->occupied = 'yes';
                            $room->save();
                        }
                    
                }
                
            }
        } 
    }
    public function updatedCustomerquery($query)
    {
        if(strlen($query) >= 3)
        {
            $this->customer = null;
            // $this->customers = Room::where('room_name', 'like', "%".strtolower($query)."%")->where('status','Active')->take(2)->with('room_bookings')->get();
            $this->customers = Room::where('room_name', 'like', "%".strtolower($query)."%")
                    ->where('status', 'Active')
                    ->take(2)
                    ->with(['room_bookings' => function($query) {
                        $query->whereIn('status', ['Invoiced', 'Pending']);
                    }])
                    ->get();
                        }
    }
    public function addItem($item)
    {
        $this->item = $item;
        $this->items = [];

        // Find the booking by ID
        $booking = RoomBooking::find($item['id']);

        if ($booking) {
            // Convert end_date to Carbon and add one day
            $booking->end_date = Carbon::parse($booking->end_date)->addDay();
            $booking->save();
        }
    }
    
    public function deleteItem($item)
    {
        $this->item = $item;
        $this->items = [];
       
            $item = RoomBooking::find($item['id']);
            if ($item) {
                $item->delete();

            }
            $room = Room::find($item['room_id']);
            if ($room) {
                // Convert end_date to Carbon and add one day
                $room->occupied = 'no';
                $room->save();
            }
        
    }
    public function removeItem($item)
    {
        $this->item = $item;
        $this->items = [];

        // Find the booking by ID
        $booking = RoomBooking::find($item['id']);

        if ($booking) {
            // Convert end_date to Carbon and add one day
            $booking->end_date = Carbon::parse($booking->end_date)->subDay();
            $booking->save();
        }
        
    }
    public function generateInvoice()
    {
        $Invoice = HotelInvoice::create(
            [
                'invoice_date'                => date('Y-m-d H:i:s'),
                'reg_by'                      => intval(Auth::user()->id),
                'status'                      => "Pending",
                'company_id'                  => intval(Auth::user()->company_id),
                'customer_id'                 => intval($this->Customers_details->id),
            ]);
            // dd("jj".$Invoice);
            $sale=RoomBooking::where('customer_id',$this->Customers_details->id)->where('status','Pending')->get();
            foreach ($sale as $sal) 
            {
                $user = HotelInvoiceItem::create(
                    [
                        'invoice_id'                      => $Invoice->id,
                        'room_id'                         => $sal->room_id,
                        'start_date'                      => $sal->start_date,
                        'end_date'                        => $sal->end_date,
                        'price'                           => $sal->room->rooms_category->price_day,
                        'status'                          => 'Pending',
                        'created_at'                => date('Y-m-d H:i:s'),
                    ]);

                // $sal->invoice_issued_id = $user->id;
                $sal->status = 'invoiced';
                $sal->save();
                
        
                // Find the product associated with the sale
                // $product = Product::find($sal->product_id); // Assuming 'product_id' exists in the Sale table
        
                // if ($product) {
                //     // Update qty_sold and qty_remained
                //     $product->invoiced_qty = $sal->quantity; // Assuming $sale has a 'quantity' field
                //     $product->qty_remained = $product->qty_remained - $sal->quantity;
        
                //     // Save the updated product data
                //     $product->save();
                   
                // }
            }
            $this->emit('InvoiceUpdated');
            return redirect()->to(route('customer-hotel-profile', ['id' => $this->Customers_details->id]));

    }
    


}
