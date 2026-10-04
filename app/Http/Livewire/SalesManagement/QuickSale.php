<?php

namespace App\Http\Livewire\SalesManagement;

use Livewire\Component;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;

/**
 * Mbao Quick / Cash Sale — the Mbao counterpart of
 * App\Http\Livewire\TuliSalesManagement\QuickSale.
 *
 * Differences from the Hardware version:
 *  - Mbao customer #1 is a real customer, so walk-in sales use customer_id 0.
 *  - Cart rows use their own statuses so they never mix with the regular
 *    Mbao sale flow, which keeps per-customer carts in status 'Pending'.
 *  - The cart is per cashier (sold_by) and search is limited to the
 *    cashier's store, like Mbao's OparateSales.
 *  - No invoice is created (same as Hardware): stock is reduced and rows
 *    are marked sold / sold_discounted.
 */
class QuickSale extends Component
{
    const WALK_IN_CUSTOMER = 0;
    const CART_STATUSES = ['quick_pending', 'quick_discount_pending'];
    const SOLD_STATUSES = ['sold', 'sold_discounted'];

    public $productquery = '';

    public $products = [];

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

        $sale = $this->cart()->with('product')->get();

        $this->total = 0;

        foreach ($sale as $item) {
            $this->total += $item->quantity * $item->selling_price;
        }

        $this->change = max(0, (float) $this->received - (float) $this->total);

        return view('livewire.sales-management.quick-sale', ['sale' => $sale]);
    }


    private function cart()
    {
        return Sale::where('customer_id', self::WALK_IN_CUSTOMER)
            ->where('sold_by', Auth::user()->id)
            ->whereIn('status', self::CART_STATUSES);
    }


    private function productsInScope()
    {
        $query = Product::where('status', 'Active');

        if (!Auth::user()->hasFullAccess()) {
            $query->where('store_id', Auth::user()->office_location);
        }

        return $query;
    }


    public function loadTodaySummary()
    {
        $today = date('Y-m-d');

        $sales = Sale::with('product')
            ->where('customer_id', self::WALK_IN_CUSTOMER)
            ->whereDate('date_sold', $today)
            ->whereIn('status', self::SOLD_STATUSES)
            ->when(!Auth::user()->hasFullAccess(), function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('store_id', Auth::user()->office_location);
                });
            })
            ->get();

        $this->todaySales = 0;
        $this->todayProfit = 0;
        $this->todayItems = 0;
        $this->todayTransactions = $sales->count();

        foreach ($sales as $sale) {
            $this->todaySales += $sale->selling_price * $sale->quantity;

            $profitPerItem = $sale->selling_price - optional($sale->product)->purchasing_price;
            $this->todayProfit += $profitPerItem * $sale->quantity;

            $this->todayItems += $sale->quantity;
        }

        $this->todayProducts = $sales;
    }


    /*
    |--------------------------------------------------------------------------
    | Search Product
    |--------------------------------------------------------------------------
    */

    public function updatedProductquery($value)
    {
        if (strlen($value) >= 1) {
            $this->products = $this->productsInScope()
                ->where(function ($query) use ($value) {
                    $query->where('product_name', 'like', '%'.$value.'%')
                        ->orWhere('barcode', 'like', '%'.$value.'%');
                })
                ->take(10)
                ->get();
        } else {
            $this->products = [];
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Add Product To Cart
    |--------------------------------------------------------------------------
    */

    public function selectProduct($product)
    {
        $this->quantity = (float) $this->quantity;

        if ($this->quantity <= 0) {
            $this->quantity = 0.01;
        }

        // Re-read stock from the database rather than trusting the payload.
        $product = $this->productsInScope()->find($product['id']);

        if (!$product) {
            return;
        }

        if ($product->qty_remained < $this->quantity) {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'warning',
                'message' => 'Stock Haitoshi',
                'text' => "Imebaki {$product->qty_remained} {$product->unit_of_measuer} pekee.",
            ]);

            return;
        }

        $exist = $this->cart()->where('product_id', $product->id)->first();

        if ($exist) {
            $newQty = $exist->quantity + $this->quantity;

            if ($newQty > $product->qty_remained) {
                $this->dispatchBrowserEvent('swal:modal', [
                    'type' => 'warning',
                    'message' => 'Stock Haitoshi',
                    'text' => 'Huwezi kuongeza zaidi ya stock iliyopo.',
                ]);

                return;
            }

            $exist->quantity = $newQty;
            $exist->save();
        } else {
            Sale::create([
                'product_id' => $product->id,
                'quantity' => $this->quantity,
                'original_price' => $product->selling_price,
                'selling_price' => $product->selling_price,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'sold_by' => Auth::user()->id,
                'status' => 'quick_pending',
                'date_sold' => date('Y-m-d H:i:s'),
                'company_id' => Auth::user()->company_id,
                'customer_id' => self::WALK_IN_CUSTOMER,
            ]);
        }

        $this->productquery = '';
        $this->products = [];
        $this->quantity = 1;
    }


    /*
    |--------------------------------------------------------------------------
    | Cart quantity changes
    |--------------------------------------------------------------------------
    */

    public function updateQuantity($id, $qty)
    {
        $sale = $this->cart()->with('product')->find($id);

        if (!$sale) {
            return;
        }

        $qty = (float) $qty;

        if ($qty <= 0) {
            $qty = 0.01;
        }

        if ($qty > $sale->product->qty_remained) {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'warning',
                'message' => 'Stock Haitoshi',
                'text' => "Stock iliyobaki ni {$sale->product->qty_remained}",
            ]);

            return;
        }

        $sale->quantity = $qty;
        $sale->save();
    }


    public function addItem($item)
    {
        $sale = $this->cart()->with('product')->find($item['id']);

        if (!$sale) {
            return;
        }

        $qty = $sale->quantity + 1;

        if ($qty > $sale->product->qty_remained) {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'warning',
                'message' => 'Stock Haitoshi',
                'text' => "Stock iliyobaki ni {$sale->product->qty_remained}",
            ]);

            return;
        }

        $sale->quantity = $qty;
        $sale->save();
    }


    public function removeItem($item)
    {
        $sale = $this->cart()->find($item['id']);

        if ($sale && $sale->quantity > 1) {
            $sale->quantity--;
            $sale->save();
        }
    }


    public function deleteItem($item)
    {
        optional($this->cart()->find($item['id']))->delete();
    }


    /*
    |--------------------------------------------------------------------------
    | Discount
    |--------------------------------------------------------------------------
    */

    public function applyDiscount()
    {
        $sale = $this->cart()->find($this->discount_item_id);

        if (!$sale) {
            return;
        }

        if ($this->discount_percentage > 0) {
            $discount = ($sale->original_price * $this->discount_percentage) / 100;

            $sale->discount_percent = $this->discount_percentage;
            $sale->discount_amount = $discount;
            $sale->selling_price = $sale->original_price - $discount;
            $sale->status = 'quick_discount_pending';
        } elseif ($this->discount_price > 0) {
            $discount = $sale->original_price - $this->discount_price;

            $sale->discount_amount = $discount;
            $sale->discount_percent = ($discount / $sale->original_price) * 100;
            $sale->selling_price = $this->discount_price;
            $sale->status = 'quick_discount_pending';
        }

        $sale->save();

        $this->discount_item_id = null;
        $this->discount_percentage = 0;
        $this->discount_price = 0;

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
        if ($this->received < $this->total) {
            $this->dispatchBrowserEvent('swal:modal', [
                'type' => 'warning',
                'message' => 'Amount Haitoshi',
                'text' => 'Fedha uliyopokea ni ndogo kuliko jumla ya mauzo.',
            ]);

            return;
        }

        foreach ($this->cart()->get() as $sale) {
            $product = Product::find($sale->product_id);

            $product->qty_remained -= $sale->quantity;
            $product->qty_sold += $sale->quantity;
            $product->save();

            $sale->status = $sale->discount_amount > 0 ? 'sold_discounted' : 'sold';
            $sale->date_sold = date('Y-m-d H:i:s');
            $sale->save();
        }

        $this->dispatchBrowserEvent('swal:modal', [
            'type' => 'success',
            'message' => 'Success',
            'text' => 'Cash Sale Completed Successfully.',
        ]);

        $this->received = 0;
        $this->change = 0;
    }
}
