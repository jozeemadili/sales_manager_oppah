<?php

namespace App\Http\Livewire\TuliSalesManagement;

use Livewire\Component;
use App\Models\ProductsTuli;
use App\Models\SalesTuli;
use Illuminate\Support\Facades\Auth;

class QuickSale extends Component
{

    public $customer_id = 1; // Walking Customer

    public $productquery = '';

    public $products = [];

    // public $quantity = 1;
    public $quantity = 1.00;


    public $received = 0;

    public $change = 0;

    public $total = 0;

    public $todaySales = 0;

public $todayProfit = 0;

public $todayItems = 0;

public $todayTransactions = 0;
public $todayProducts = [];



    // Discount
    public $discount_item_id;

    public $discount_percentage = 0;

    public $discount_price = 0;



    public function render()
    {

        $this->loadTodaySummary();

        $sale = SalesTuli::with('products_tuli')
            ->where('customer_id',$this->customer_id)
            ->whereIn('status',['Pending','discount_pending'])
            ->get();


        $this->total = 0;


        foreach($sale as $item)
        {
            $this->total += $item->quantity * $item->selling_price;
        }


        // $this->change = max(0,$this->received - $this->total);
        $this->change = max(0, (float)$this->received - (float)$this->total);



        return view(
            'livewire.tuli-sales-management.quick-sale',
            [
                'sale'=>$sale
            ]
        );

    }



    public function loadTodaySummary()
    {
    
        $today = date('Y-m-d');
    
    
        $sales = SalesTuli::whereDate('date_sold',$today)
            ->whereIn('status',[
                'sold',
                'sold_discounted'
            ])
            ->get();
    
    
    
        $this->todaySales = 0;
    
        $this->todayProfit = 0;
    
        $this->todayItems = 0;
    
        $this->todayTransactions = $sales->count();
    
    
    
        foreach($sales as $sale)
        {
    
    
            // Total sales
            $this->todaySales += 
            ($sale->selling_price * $sale->quantity);
    
    
    
            // Profit
            // $profitPerItem = 
            // $sale->selling_price - $sale->original_price;
            $profitPerItem = 
$sale->selling_price - $sale->products_tuli->purchasing_price;
    
    
    
            $this->todayProfit += 
            ($profitPerItem * $sale->quantity);
    
    
    
            // Total quantity sold
            $this->todayItems += 
            $sale->quantity;
    
    
        }
        $this->todayProducts = SalesTuli::with(['products_tuli', 'user'])
    ->whereDate('date_sold',$today)
    // ->whereIn('status',[
    //     'sold',
    //     'sold_discounted'
    // ])
    ->get();
    
    
    }

    /*
    |--------------------------------------------------------------------------
    | Search Product
    |--------------------------------------------------------------------------
    */

    public function updatedProductquery($value)
    {

        if(strlen($value) >= 1)
        {

            // $this->products = ProductsTuli::where('store_id',Auth::user()->office_location)
            $this->products = ProductsTuli::where('status','Active')
                ->where(function($query) use ($value){

                    $query->where(
                        'product_name',
                        'like',
                        '%'.$value.'%'
                    )
                    ->orWhere(
                        'barcode',
                        'like',
                        '%'.$value.'%'
                    );

                })
                ->take(10)
                ->get();

        }
        else
        {

            $this->products=[];

        }

    }





    /*
    |--------------------------------------------------------------------------
    | Add Product To Cart
    |--------------------------------------------------------------------------
    */


    public function selectProduct($product)
    {


        // if($this->quantity <=0)
        // {
        //     $this->quantity=1;
        // }
        $this->quantity = (float) $this->quantity;

if ($this->quantity <= 0) {
    $this->quantity = 0.01;
}



        if($product['qty_remained'] < $this->quantity)
        {

            $this->dispatchBrowserEvent('swal:modal',[

                'type'=>'warning',

                'message'=>'Stock Haitoshi',

                'text'=>"Imebaki {$product['qty_remained']} {$product['unit_of_measuer']} pekee."

            ]);


            return;

        }




        $exist = SalesTuli::where('product_id',$product['id'])
            ->where('customer_id',$this->customer_id)
            ->whereIn('status',['Pending','discount_pending'])
            ->first();




        if($exist)
        {


            $newQty = $exist->quantity + $this->quantity;



            if($newQty > $product['qty_remained'])
            {

                $this->dispatchBrowserEvent('swal:modal',[

                    'type'=>'warning',

                    'message'=>'Stock Haitoshi',

                    'text'=>'Huwezi kuongeza zaidi ya stock iliyopo.'

                ]);


                return;

            }



            $exist->quantity=$newQty;

            $exist->save();



        }
        else
        {



            SalesTuli::create([


                'product_id'=>$product['id'],


                'quantity'=>$this->quantity,



                'original_price'=>$product['selling_price'],


                'selling_price'=>$product['selling_price'],


                'discount_percent'=>0,


                'discount_amount'=>0,



                'sold_by'=>Auth::user()->id,


                'status'=>'Pending',


                'date_sold'=>date('Y-m-d H:i:s'),


                'company_id'=>Auth::user()->company_id,


                'customer_id'=>$this->customer_id,


            ]);

        }



        $this->productquery='';

        $this->products=[];

        $this->quantity=1;


    }





    /*
    |--------------------------------------------------------------------------
    | Update Quantity From Input
    |--------------------------------------------------------------------------
    */


    public function updateQuantity($id,$qty)
    {


        $sale = SalesTuli::with('products_tuli')
            ->find($id);



        if(!$sale)
        {
            return;
        }



        // if($qty < 1)
        // {
        //     $qty=1;
        // }

        $qty = (float) $qty;

if ($qty <= 0) {
    $qty = 0.01;
}




        if($qty > $sale->products_tuli->qty_remained)
        {


            $this->dispatchBrowserEvent('swal:modal',[

                'type'=>'warning',

                'message'=>'Stock Haitoshi',

                'text'=>"Stock iliyobaki ni {$sale->products_tuli->qty_remained}"

            ]);

            return;

        }



        $sale->quantity=$qty;

        $sale->save();


    }





    /*
    |--------------------------------------------------------------------------
    | Increase Quantity
    |--------------------------------------------------------------------------
    */


    public function addItem($item)
    {


        $sale = SalesTuli::with('products_tuli')
            ->find($item['id']);



        if(!$sale)
        {
            return;
        }



        $qty=$sale->quantity+1;
        



        if($qty > $sale->products_tuli->qty_remained)
        {


            $this->dispatchBrowserEvent('swal:modal',[

                'type'=>'warning',

                'message'=>'Stock Haitoshi',

                'text'=>"Stock iliyobaki ni {$sale->products_tuli->qty_remained}"

            ]);

            return;

        }



        $sale->quantity=$qty;

        $sale->save();


    }





    /*
    |--------------------------------------------------------------------------
    | Decrease Quantity
    |--------------------------------------------------------------------------
    */


    public function removeItem($item)
    {


        $sale = SalesTuli::find($item['id']);



        if($sale && $sale->quantity > 1)
        {

            $sale->quantity--;

            $sale->save();

        }


    }





    public function deleteItem($item)
    {

        SalesTuli::find($item['id'])->delete();

    }





    /*
    |--------------------------------------------------------------------------
    | Discount
    |--------------------------------------------------------------------------
    */


    public function applyDiscount()
    {


        $sale = SalesTuli::find($this->discount_item_id);



        if(!$sale)
        {
            return;
        }




        if($this->discount_percentage > 0)
        {



            $discount =
            ($sale->original_price * $this->discount_percentage)/100;



            $sale->discount_percent =
            $this->discount_percentage;



            $sale->discount_amount =
            $discount;



            $sale->selling_price =
            $sale->original_price - $discount;



            $sale->status='discount_pending';



        }
        elseif($this->discount_price > 0)
        {



            $discount =
            $sale->original_price - $this->discount_price;



            $sale->discount_amount=$discount;



            $sale->discount_percent =
            ($discount/$sale->original_price)*100;



            $sale->selling_price =
            $this->discount_price;



            $sale->status='discount_pending';



        }



        $sale->save();



        $this->discount_item_id=null;

        $this->discount_percentage=0;

        $this->discount_price=0;
        $this->dispatchBrowserEvent('close-discount-modal');


    }



    public function openDiscount($id)
    {
        $this->discount_item_id = $id;
    
        $this->discount_percentage = 0;
    
        $this->discount_price = 0;
    
    
        $this->dispatchBrowserEvent('open-discount-modal');
    }

    /*
    |--------------------------------------------------------------------------
    | Complete Cash Sale
    |--------------------------------------------------------------------------
    */


    public function completeSale()
    {


        if($this->received < $this->total)
        {

            $this->dispatchBrowserEvent('swal:modal',[

                'type'=>'warning',

                'message'=>'Amount Haitoshi',

                'text'=>'Fedha uliyopokea ni ndogo kuliko jumla ya mauzo.'

            ]);


            return;

        }




        $sales = SalesTuli::where('customer_id',$this->customer_id)
            ->whereIn('status',['Pending','discount_pending'])
            ->get();




        foreach($sales as $sale)
        {


            $product = ProductsTuli::find($sale->product_id);



            $product->qty_remained -= $sale->quantity;


            $product->qty_sold += $sale->quantity;



            $product->save();





            if($sale->discount_amount > 0)
            {

                $sale->status='sold_discounted';

            }
            else
            {

                $sale->status='sold';

            }



            $sale->save();


        }





        $this->dispatchBrowserEvent('swal:modal',[

            'type'=>'success',

            'message'=>'Success',

            'text'=>'Cash Sale Completed Successfully.'

        ]);



        $this->received=0;

        $this->change=0;


    }


}