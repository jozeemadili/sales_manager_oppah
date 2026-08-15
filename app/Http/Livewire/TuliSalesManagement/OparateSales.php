<?php

namespace App\Http\Livewire\TuliSalesManagement;


use App\Models\InvoicesTuli;
use App\Models\InvoiceItemsTuli;
use App\Models\ProductsTuli;
use App\Models\SalesTuli;
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
        $sale=SalesTuli::where('customer_id',$this->Customers_details->id)->where('status','Pending')->get();
        return view('livewire.tuli-sales-management.oparate-sales',['sale'=>$sale]);
    }
    public function generateInvoice()
    {
       
        $invoiceDate = Carbon::now();
        $saleItems = SalesTuli::where('customer_id', $this->Customers_details->id)
                        ->where('status', 'Pending')
                        ->get();
    
        // 🧮 Calculate total amount from pending sales
        $totalAmount = 0;
    
        foreach ($saleItems as $s) {
            $totalAmount += $s->quantity * $s->selling_price;
        }
    
        $Invoice = InvoicesTuli::create([
            'invoice_date'           => $invoiceDate,
            'reg_by'                 => intval(Auth::user()->id),
            'status'                 => "Pending",
            'company_id'             => intval(Auth::user()->company_id),
            'customer_id'            => intval($this->Customers_details->id),
            'total_invoice_amount'   => $totalAmount,
            'amount_paid'            => 0,
            'amount_remained'        => $totalAmount,
        ]);
        // dd($Invoice->id);
        foreach ($saleItems as $sal) {
            $user = InvoiceItemsTuli::create([
                'invoice_id' => $Invoice->id,
                'product_id' => $sal->product_id,
                'qty'        => $sal->quantity,
                'price'      => $sal->selling_price,
                'status'     => 'Pending',
            ]);
    
            $sal->invoice_issued_id = $Invoice->id;
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
   
        $sale=SalesTuli::where('customer_id',$this->Customers_details->id)->where('status','Pending')->get();
        if ($sale) 
        {
            foreach ($sale as $sal) 
            {
                // Update the sale status to 'sold'
                $sal->status = 'sold';
                $sal->save();
                
        
                // Find the product associated with the sale
                $product = ProductsTuli::find($sal->product_id); // Assuming 'product_id' exists in the Sale table
        
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
    
    // public function addItem($item)
    // {
    //     dd($item);
    //     $this->item = $item;
    //     $this->items = [];
    //     // dd($item['quantity']);
    //     // Validate if the selected ID exists
       
    //         $item = SalesTuli::find($item['id']);
    //         if ($item) {
    //             $item->quantity = $item['quantity']+1; 
    //             $item->save();
    //         }
        
    // }

    public function addItem($item)
{
    $sale = SalesTuli::with('products_tuli')->find($item['id']);

    if (!$sale) {
        return;
    }

    $qtyRemained = $sale->products_tuli->qty_remained;

    // Quantity baada ya kuongeza
    $newQuantity = $sale->quantity + 1;

    if ($newQuantity > $qtyRemained) {

        $this->dispatchBrowserEvent('swal:modal', [
            'type'    => 'warning',
            'message' => 'Stock Haitoshi',
            'text'    => "Samahani, stock iliyobaki ni {$qtyRemained} {$sale->products_tuli->unit_of_measuer} pekee. Huwezi kuongeza quantity zaidi ya stock iliyopo.",
        ]);
    
        return;
    }

    $sale->quantity = $newQuantity;
    $sale->save();
}
    
    public function deleteItem($item)
    {
        $this->item = $item;
        $this->items = [];
       
            $item = SalesTuli::find($item['id']);
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
       
            $item = SalesTuli::find($item['id']);
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
                $item = ProductsTuli::where('id', $customer['id'])
                ->where('qty_remained', '>=', $this->quantity_new)
                ->first();

            if (!$item) {
            session()->flash('error', 'Insufficient stock available. Check Remained Quantity Or Edit Quantity!!');
            return;
            }

            $this->customer = $customer;
            $this->customerquery = strtoupper($customer['product_name']);
            $this->customers = [];

            $item = SalesTuli::where('product_id', $customer['id'])->where('customer_id', $this->Customers_details->id)->where('status','Pending')->first();

            if (!is_null($item)) {
                // Update the quantity by adding 1
                $item->quantity = $item->quantity + 1;
                $item->save();
            }
            else
            {
                // dd("test ".$customer['id']."");
                $user = SalesTuli::create(
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
                // $this->customers = ProductsTuli::where('store_id',Auth::user()->office_location)->where('status','Active')->where('product_name', 'like', "%".strtolower($query)."%")->take(4)->get();
                $this->customers = ProductsTuli::where('store_id',Auth::user()->office_location)->where('status','Active')->where('product_name', 'like', "%".strtolower($query)."%")->take(4)->get();
       
       
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



