<?php

namespace App\Http\Livewire\SalesManagement;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Sale;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OparateSales extends Component
{
    public $Customers_details;
    public $customerquery;
    public $customer;
    public $item;
    public $change_status="forward";
    public $req_id;
    public $description;
    public $selectedId;

    // public $item;
    public $quantity;
    public $discount;
    public $quantity_new;

    protected $listeners = ['setItem'];

    
    public $customers = [];
    public $items=[];

  

    public function mount()
        {
            
            if($this->customer != null)
            {
                $this->customerquery = $this->customer->product_name ." | ". $this->customer->barcode;
            
            }
        }
    public function render()
    {
        $sale=Sale::where('customer_id',$this->Customers_details->id)->where('status','Pending')->get();
        return view('livewire.sales-management.oparate-sales',['sale'=>$sale]);
    }
    public function generateInvoice()
    {
       
        $invoiceDate = Carbon::now();
        $saleItems = Sale::where('customer_id', $this->Customers_details->id)
                        ->where('status', 'Pending')
                        ->get();
    
        // 🧮 Calculate total amount from pending sales
        $totalAmount = 0;
    
        foreach ($saleItems as $s) {
            $totalAmount += $s->quantity * $s->selling_price;
        }
    
       
    
        $Invoice = Invoice::create([
            'invoice_date'           => $invoiceDate,
            'reg_by'                 => intval(Auth::user()->id),
            'status'                 => "Pending",
            'company_id'             => intval(Auth::user()->company_id),
            'customer_id'            => intval($this->Customers_details->id),
            'total_invoice_amount'   => $totalAmount,
            'amount_paid'            => 0,
            'amount_remained'        => $totalAmount,
        ]);
    
        foreach ($saleItems as $sal) {
            $user = InvoiceItem::create([
                'invoice_id' => $Invoice->id,
                'product_id' => $sal->product_id,
                'qty'        => $sal->quantity,
                'price'      => $sal->selling_price,
                'status'     => 'Pending',
            ]);
    
            $sal->invoice_issued_id = $user->id;
            $sal->status = 'invoiced';
            $salSaved = $sal->save();
        }
    
        if ($salSaved) {
            $this->dispatchBrowserEvent('swal:modal', [
                'type'    => 'success',
                'message' => 'GOOD',
                'text'    => 'Invoice generated successfully: ',
            ]);
        
            $this->dispatchBrowserEvent('show-print-button', [
                'url' => route('invoice-download', ['id' => $Invoice->id])
            ]);
        }
        
     else {
            $this->dispatchBrowserEvent('swal:modal', [
                'type'    => 'error',
                'message' => 'FAILED',
                'text'    => 'Failed to save invoice.',
            ]);
        }
        // $this->emit('InvoiceUpdated');
    }



    public function operateSales()
    {
   
        $sale=Sale::where('customer_id',$this->Customers_details->id)->where('status','Pending')->get();
        if ($sale) 
        {
            foreach ($sale as $sal) 
            {
                // Update the sale status to 'sold'
                $sal->status = 'sold';
                $sal->save();
                
        
                // Find the product associated with the sale
                $product = Product::find($sal->product_id); // Assuming 'product_id' exists in the Sale table
        
                if ($product) {
                    // Update qty_sold and qty_remained
                    $product->qty_sold = $sal->quantity; // Assuming $sale has a 'quantity' field
                    $product->qty_remained = $product->qty_remained - $sal->quantity;
        
                    // Save the updated product data
                    $product->save();
                   
                }
            }
        //     // Flash success message to session
        // session()->flash('message', 'Sales updated and products adjusted successfully!');
         // Emit an event for success notification
         $this->emit('salesUpdated');

        }
    
    }
    
    public function addItem($item)
    {
        $this->item = $item;
        $this->items = [];
        // dd($item['quantity']);
        // Validate if the selected ID exists
       
            $item = Sale::find($item['id']);
            if ($item) {
                $item->quantity = $item['quantity']+1; 
                $item->save();
            }
        
    }
    
    public function deleteItem($item)
    {
        $this->item = $item;
        $this->items = [];
       
            $item = Sale::find($item['id']);
            if ($item) {
                $item->delete();

            }
        
    }
    public function removeItem($item)
    {
        $this->item = $item;
        $this->items = [];
        // dd($item['quantity']);
        // Validate if the selected ID exists
       
            $item = Sale::find($item['id']);
            if ($item) {
                $item->quantity = $item['quantity']-1; 
                $item->save();
            }
        
    }
    public function selectCustomer($customer)
        {
            // dd($this->quantity_new);
            if(empty($this->quantity_new))
            {
                session()->flash('error', 'Please To Insert Quantity to be sold.');
                return;
            }
            // Proceed with your logic here
                $item = Product::where('id', $customer['id'])
                ->where('qty_remained', '>=', $this->quantity_new)
                ->first();

            if (!$item) {
            session()->flash('error', 'Insufficient stock available. Check Remained Quantity Or Edit Quantity!!');
            return;
            }

            $this->customer = $customer;
            $this->customerquery = strtoupper($customer['product_name']);
            $this->customers = [];

            $item = Sale::where('product_id', $customer['id'])->where('customer_id', $this->Customers_details->id)->where('status','Pending')->first();

            if (!is_null($item)) {
                // Update the quantity by adding 1
                $item->quantity = $item->quantity + 1;
                $item->save();
            }
            else
            {
                // dd("test ".$customer['id']."");
                $user = Sale::create(
                    [
                        'product_id'                 => $customer['id'],
                        'quantity'                   => $this->quantity_new,
                        'selling_price'              => $customer['selling_price'],
                        'sold_by'                    => intval(Auth::user()->id),
                        'status'                     => "Pending",
                        'date_sold'                  => date('Y-m-d H:i:s'),
                        'company_id'                 => intval(Auth::user()->company_id),
                        'customer_id'                => intval($this->Customers_details->id),
                    ]);
                    
            }
            
        }
        public function updatedCustomerquery($query)
        {
            if(strlen($query) >= 1)
            {
                $this->customer = null;
                // $this->customers = Product::where('store_id',Auth::user()->office_location)->where('product_name', 'like', "%".strtolower($query)."%")->orWhere('barcode', 'like', "%".$query."%")->take(4)->get();
                $this->customers = Product::where('store_id',Auth::user()->office_location)->where('status','Active')->where('product_name', 'like', "%".strtolower($query)."%")->take(4)->get();
       
            }
        }
        public function setItem($item)
            {
                $this->item = $item;
                $this->quantity = $item['quantity'];  // Set initial quantity
                $this->discount = $item['discount'] ?? 0;  // Set initial discount
            }

            public function addQuantity()
            {
                // Logic to add quantity
                // You can use $this->item to access the current item
                $this->item->quantity = $this->quantity;
                // Save the updated quantity in the database or session
                $this->emit('itemUpdated', $this->item);
            }

            public function applyDiscount()
            {
                // Logic to apply discount
                $this->item->discount = $this->discount;
                // Save the discount in the database or session
                $this->emit('itemUpdated', $this->item);
            }
    
}



