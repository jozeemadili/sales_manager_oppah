<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use App\Models\Category;
use App\Models\EditedProduct;
use App\Models\Expense;
use App\Models\ExpensesRecord;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductsInventory;
use App\Models\ProductTransferDetail;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;



class ProductsController extends Controller
{
    public function get()
    {
        $barcodeValue = rand(1000000000, 9999999999);

      
            $stores = Store::where('company_id',Auth::user()->company_id)->where('status','Active')->orderBy('id','desc')->get();
            $Categories = Category::where('company_id',Auth::user()->company_id)->where('status','Active')->orderBy('id','desc')->get();
               if(Auth::user()->role == 'ADMIN')
                {
                    $Branch = Product::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
        
                }
                else{
                    $Branch = Product::where('company_id',Auth::user()->company_id)->where('store_id',Auth::user()->office_location)->orderBy('id','desc')->paginate(10);
        
               }
           return view('admin.sales_management.products-registration',['Branch' => $Branch,'Categories'=>$Categories,'stores'=>$stores,'barcodeValue'=>$barcodeValue]);
    }
    public function searchProduct(Request $request)
    {
        $loans_datails = Product::query();

            if (!empty($request->cname)) {
                $loans_datails->where('product_name', 'like', '%' . $request->cname . '%');
            }
        $loans_datails = $loans_datails->orderBy('id', 'desc')->paginate(10);

        $barcodeValue = rand(1000000000, 9999999999);

      
            $stores = Store::where('company_id',Auth::user()->company_id)->where('status','Active')->orderBy('id','desc')->get();
            $Categories = Category::where('company_id',Auth::user()->company_id)->where('status','Active')->orderBy('id','desc')->get();
            // $Branch = Product::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
           return view('admin.sales_management.products-registration',['Branch' => $loans_datails,'Categories'=>$Categories,'stores'=>$stores,'barcodeValue'=>$barcodeValue]);
    }
    public function getEditedProduct()
    {
        $barcodeValue = rand(1000000000, 9999999999);

            $stores = Store::where('company_id',Auth::user()->company_id)->where('status','Active')->orderBy('id','desc')->get();
            $Categories = Category::where('company_id',Auth::user()->company_id)->where('status','Active')->orderBy('id','desc')->get();
            $Branch = Product::where('company_id',Auth::user()->company_id)->where('status','edited')->orderBy('id','desc')->paginate(10);
           return view('admin.sales_management.products-edited',['Branch' => $Branch,'Categories'=>$Categories,'stores'=>$stores,'barcodeValue'=>$barcodeValue]);
    }
    public function getTransferedProduct()
    {
        $barcodeValue = rand(1000000000, 9999999999);

            $stores = Store::where('company_id',Auth::user()->company_id)->where('status','Active')->orderBy('id','desc')->get();
            $Categories = Category::where('company_id',Auth::user()->company_id)->where('status','Active')->orderBy('id','desc')->get();
            $Branch = Product::where('company_id',Auth::user()->company_id)->where('status','edited_transfer')->orderBy('id','desc')->paginate(10);
           return view('admin.sales_management.products-edited',['Branch' => $Branch,'Categories'=>$Categories,'stores'=>$stores,'barcodeValue'=>$barcodeValue]);
    }
    public function productPreview($id) 
    {
        $Products = Product::with('edited_products')->find($id);
        $transfer_details = ProductTransferDetail::where('product_id',$id)->get();
        return view('admin.sales_management.product-preview',['Products' => $Products,'transfer_details'=>$transfer_details]);
    }
    public function categoryPreview($id) 
    {
        if(Auth::user()->role == 'ADMIN')
        {
            $Products = Product::where('category',$id)->paginate(10);
            $AllProducts = Product::where('category',$id)->get();
        }
        else{
            $Products = Product::where('category',$id)->where('store_id',Auth::user()->office_location)->paginate(10);
            $AllProducts = Product::where('category',$id)->where('store_id',Auth::user()->office_location)->get();

        }
       
        return view('admin.sales_management.category-preview',['Products' => $Products,'AllProducts'=>$AllProducts]);
    }
    public function InventoryPreview($id) 
    {
        $barcodeValue = rand(1000000000, 9999999999);

        // $Products = Product::where('status','Active')->get();
        $Inventory = Inventory::find($id);
        $Products = Product::where('status','Active')->where('store_id',$Inventory->store_id)->get();
        $Branch = ProductsInventory::where('company_id',Auth::user()->company_id)->where('inventory_id',$id)->orderBy('id','desc')->paginate(10);
        $stores = Store::where('company_id',Auth::user()->company_id)->where('status','Active')->orderBy('id','desc')->get();
        $Categories = Category::where('company_id',Auth::user()->company_id)->where('status','Active')->orderBy('id','desc')->get();
        // $Expense = Expense::where('company_id',Auth::user()->company_id)->where('status','Active')->orderBy('id','desc')->get();
        $Expense = Expense::where('company_id',Auth::user()->company_id)->where('status','Active')->where('to_be_used','MBAO')->orderBy('id','desc')->get();
        $ExpensesRecord = ExpensesRecord::where('company_id',Auth::user()->company_id)->where('inventory_id',$id)->orderBy('id','desc')->get();

        $AllProducts = ProductsInventory::where('company_id',Auth::user()->company_id)->where('inventory_id',$id)->orderBy('id','desc')->get();
        // dd($ExpensesRecord);
           
   
        return view('admin.sales_management.inventory-preview',['Products' => $Products,'Inventory'=>$Inventory,'Branch'=>$Branch,'stores'=>$stores,'Categories'=>$Categories,'barcodeValue'=>$barcodeValue,'Expense'=>$Expense,'ExpensesRecord'=>$ExpensesRecord,'AllProducts'=>$AllProducts]);
    }
    public function SupplierPreview($name) 
    {
        // dd($name);
     
        $Inventory = Inventory::where('company_name',$name)->paginate(10);
           
   
        return view('admin.sales_management.suppliers-list-items',['Branch' => $Inventory]);
    }
    public function storePreview($id) 
    {
     
        $Products = Product::where('store_id',$id)->paginate(10);
        $AllProducts = Product::where('store_id',$id)->get();
        return view('admin.sales_management.store-preview',['Products' => $Products,'AllProducts'=>$AllProducts]);
    }
    public function updateProductEditedStatus(Request $request)
    {
        if($request->status=="Approve")
        {
            $product_edit = Product::find($request->id);
            $product_edit->status = "Active"; 
            $product_edit->save();
            return redirect()->back()->with('success', 'Product Approved  successful.');
        }elseif($request->status=="Reject")
        {
            $product_edit = Product::find($request->id);
            $product_edit->status = "Rejected"; 
            $product_edit->save();
            return redirect()->back()->with('success', 'Product Rejected  successful.');
        }
        
    }
    // public function sendProductsTostock(Request $request)
    // {
    //     // dd("ttt".$request->id);
    //     $inventoryId=$request->id;
        
    //      $inventory = DB::table('inventories')->where('id', $inventoryId)->first();

    // // If already submitted, prevent reprocessing
    // if (!$inventory || $inventory->status === 'submitted') {
    //     return redirect()->back()->with('warning', 'Stock already submitted.');
    // }

    //     DB::transaction(function () use ($inventoryId) {
    //         $records = DB::table('products_inventories')
    //             ->where('inventory_id', $inventoryId)
    //             ->get();
    
    //         foreach ($records as $item) {
    //             // Convert to array
    //             $itemArray = (array) $item;
    
    //             // Try to find existing product by barcode
    //             $existing = DB::table('products')
    //                 ->where('product_name', $item->product_name)
    //                 ->where('store_id', $inventory->store_id) // ✅ store-specific
    //                 ->first();
    
    //             if ($existing) {
    //                 // If exists, update qty_remained
    //                 DB::table('products')
    //                     ->where('product_name', $item->product_name)
    //                     ->update([
    //                         'qty' => $existing->qty + $item->qty, // Optional: update other fields if needed
    //                         'qty_remained' => $existing->qty_remained + $item->qty,
    //                         'purchasing_price' => $item->purchasing_price, 
    //                         'selling_price' => $item->selling_price, 
    //                         'store_id'     => $inventory->store_id, // ✅ ENSURE store_id
    //                     ]);
    //             } else {
    //                 // If not exists, insert new record with qty_remained = qty
    //                 $itemArray['qty_remained'] = $item->qty;
    //                 DB::table('products')->insert($itemArray);
    //             }
    //         }
    
    //         // Update status in products_inventories
    //         DB::table('products_inventories')
    //             ->where('inventory_id', $inventoryId)
    //             ->update(['status' => 'approved']);

    //             // Update status in products_inventories
    //         DB::table('inventories')
    //         ->where('id', $inventoryId)
    //         ->update(['status' => 'submitted']);
    //     });
    //     return redirect()->back()->with('success', 'Stock Submited  successful.');
        
    // }

    public function sendProductsTostock(Request $request)
{
    $inventoryId = $request->id;

    $inventory = DB::table('inventories')->where('id', $inventoryId)->first();

    // If already submitted, prevent reprocessing
    if (!$inventory || $inventory->status === 'submitted') {
        return redirect()->back()->with('warning', 'Stock already submitted.');
    }

    DB::transaction(function () use ($inventoryId, $inventory) {
        $records = DB::table('products_inventories')
            ->where('inventory_id', $inventoryId)
            ->get();

        foreach ($records as $item) {
            // Convert to array
            $itemArray = (array) $item;

            // Try to find existing product
            $existing = DB::table('products')
                ->where('product_name', $item->product_name)
                ->where('store_id', $inventory->store_id)
                ->first();

            if ($existing) {
                DB::table('products')
                    ->where('product_name', $item->product_name)
                    ->where('store_id', $inventory->store_id)
                    ->update([
                        'qty' => $existing->qty + $item->qty,
                        'qty_remained' => $existing->qty_remained + $item->qty,
                        'purchasing_price' => $item->purchasing_price,
                        'selling_price' => $item->selling_price,
                        'store_id' => $inventory->store_id, // ✅ added
                        'status' => 'Active',
                    ]);
            } else {
                // Insert new product
                $itemArray['qty_remained'] = $item->qty;
                $itemArray['store_id'] = $inventory->store_id; // ✅ added
                $itemArray['status'] = 'Active'; // ✅ ADD THIS LINE

                DB::table('products')->insert($itemArray);
            }
        }

        DB::table('products_inventories')
            ->where('inventory_id', $inventoryId)
            ->update(['status' => 'approved']);

        DB::table('inventories')
            ->where('id', $inventoryId)
            ->update(['status' => 'submitted']);
    });

    return redirect()->back()->with('success', 'Stock Submited successful.');
}


    public function updateProductTransferStatus(Request $request)
    {
        if($request->status=="Approve")
        {
            $product_edit = Product::find($request->id);
            $product_edit->status = "Active"; 
            $product_edit->save();
            return redirect()->back()->with('success', 'Product Approved  successful.');
        }elseif($request->status=="Reject")
        {
            $product_edit = Product::find($request->id);
            $product_edit->status = "Rejected"; 
            $product_edit->save();
            return redirect()->back()->with('success', 'Product Rejected  successful.');
        }
        
    }
    public function oparateSale()
    {
            $stores = Store::where('company_id',Auth::user()->company_id)->where('status','Active')->orderBy('id','desc')->get();
            $Categories = Category::where('company_id',Auth::user()->company_id)->where('status','Active')->orderBy('id','desc')->get();
            $Branch = Product::where('company_id',Auth::user()->company_id)->orderBy('id','desc')->paginate(10);
           return view('admin.sales_management.operate-sale',['Branch' => $Branch,'Categories'=>$Categories,'stores'=>$stores]);
    }
    public function TransferProduct(Request $request)
    {   
    
        
            $product = Product::find($request->product_id);

            if ($product) {
            
                $ProductTransferDetail = new ProductTransferDetail();
                $ProductTransferDetail->product_id = $product->id;
                $ProductTransferDetail->from_store = $product->store_id;
                $ProductTransferDetail->to_store = $request->new_store_id;
                $ProductTransferDetail->transfer_details = $request->reason_for_transfer;
                $ProductTransferDetail->quantity_remained = $product->qty_remained;
                $ProductTransferDetail->quantity_transfered = $request->qty;
                $ProductTransferDetail->status = 'Approved';
                $ProductTransferDetail->transfered_by = auth()->user()->id;
                $ProductTransferDetail->date_transfared = Carbon::now();
                $ProductTransferDetail->save();

                //update quantity remained 
                $product_edit = Product::find($request->product_id);
                $product_edit->status = "edited_transfer"; 
                $product_edit->qty_remained = $product_edit->qty_remained-$request->qty;
                $product_edit->qty = $product_edit->qty-$request->qty;
                $product_edit->save();

                //Add new detail for new store
                $newProductForNewstore = new Product();
                $newProductForNewstore->product_name = $product->product_name;
                $newProductForNewstore->qty = $request->qty;
                $newProductForNewstore->category = $product->category;
                $newProductForNewstore->store_id = $request->new_store_id;
                $newProductForNewstore->barcode = $product->barcode;
                $newProductForNewstore->purchasing_price = $product->purchasing_price;
                $newProductForNewstore->selling_price = $product->selling_price;
                $newProductForNewstore->description = $product->description;
                $newProductForNewstore->company_id = $product->company_id;
                $newProductForNewstore->expire_date = $product->expire_date;
                $newProductForNewstore->created_by = auth()->user()->id;
                $newProductForNewstore->reg_at = Carbon::now();
                $newProductForNewstore->status = "edited_transfer";
                $newProductForNewstore->qty_sold = 0;
                $newProductForNewstore->invoiced_qty = 0;
                $newProductForNewstore->qty_remained = $request->qty;
                $newProductForNewstore->unit_of_measuer = $product->unit_of_measuer;
                $newProductForNewstore->save();
    


                return redirect()->back()->with('success', 'Product Updated  successful.');
            } 
            else 
            {
                return redirect()->back()->with('success', 'Product Not  Found.');
            }  
    }
    public function EditProduct(Request $request)
    {   
    
    $product = Product::find($request->product_id);

    if ($product) {
       
        $editedProduct = new EditedProduct();
        $editedProduct->product_id = $product->id;
        $editedProduct->product_name = $product->product_name;
        $editedProduct->qty = $product->qty;
        $editedProduct->category = $product->category;
        $editedProduct->purchasing_price = $product->purchasing_price;
        $editedProduct->selling_price = $product->selling_price;
        $editedProduct->description = "na";
        $editedProduct->unit_of_measuer = $product->unit_of_measuer;
        $editedProduct->edited_by = auth()->user()->id; 
        $editedProduct->reason_for_editing = $request->reason_for_editing;
        $editedProduct->date_edited = Carbon::now();
        $editedProduct->save();

        $product_edit = Product::find($request->product_id);

        if($request->qty < 0)
        {
            // For negative quantities, subtracting from qty
            $qty_remained = $product_edit->qty_remained + intval($request->qty);
            $qty_recorded = $product_edit->qty + intval($request->qty);
            // dd("negative value");
        }
        elseif($request->qty > 0)
        {
            // For positive quantities, adding to qty
            $qty_remained = $product_edit->qty_remained + $request->qty;
            $qty_recorded = $product_edit->qty + $request->qty;
            // dd("positive value");
        }
        else
        {
            // When qty is exactly 0, update to 0
            $qty_remained = $product_edit->qty_remained;
            $qty_recorded = $product_edit->qty;
        }
        

        $product_edit->status = "edited"; 
        $product_edit->product_name = $request->product_name;
        $product_edit->qty = $qty_recorded;
        $product_edit->unit_of_measuer = $request->unit_of_measuer;
        $product_edit->category = $request->category;
        $product_edit->purchasing_price = $request->purchasing_price;
        $product_edit->selling_price = $request->selling_price;
        $product_edit->qty_remained =  $qty_remained;
        $product_edit->save();

        return redirect()->back()->with('success', 'Product Updated  successful.');
    } else {
        return redirect()->back()->with('success', 'Product Not  Found.');
    }
       
          
    }

    public function import(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);
                // Additional data you want to pass 
        $additionalData = [
            'year' => $request->year,
            'status' => $request->status,
            // Add more key-value pairs as needed 
        ];
        // dd($additionalData['status']);
        // Get the uploaded file
        $file = $request->file('file');
 
        // Process the Excel file
        // Excel ::import(new ProductsImport($additionalData), $file);
        // Excel::import(new CustomersImport($additionalData), $request->file('file'));
 
        return redirect()->back()->with('success', 'Excel file imported successfully!');
    }
    public function registerInventory(Request $request)
    {   
		// 'expire_date',
        $request->validate([
            // 'product_name' => 'required|string|max:255',
            'qty' => 'required|integer',
            'category' => 'required|string|max:255',
            'barcode' => 'required|string|max:100|unique:products,barcode',
            'purchasing_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);
        // Check for duplicate
        $existingProduct = ProductsInventory::where('product_name', $request->product_name)
            ->where('inventory_id', $request->inventory_id)
            ->first();

        if ($existingProduct) {
            return redirect()->back()->with('error', 'Product with name <b>' . strtoupper($request->product_name) . '</b> already exists in this inventory.');
        }
            
        $inventory = Inventory::findOrFail($request->inventory_id);

            $user = ProductsInventory::create(   
                [
                    'product_name'          => $request->product_name,
                    'qty'                   => $request->qty,
                    'qty_remained'          => $request->qty,
                    'unit_of_measuer'       => $request->unit_of_measuer,
                    'category'              => $request->category,
                    'barcode'               => $request->barcode,
                    'purchasing_price'      => $request->purchasing_price,
                    'selling_price'         => $request->selling_price,
                    'description'           => $request->description,
                    'store_id'              => $inventory->store_id,
                    'company_id'            => intval(Auth::user()->company_id),
                    'created_by'            => intval(Auth::user()->id),
                    'status'                => 'Waiting',
                    'reg_at'                => date('Y-m-d H:i:s'),
                    'inventory_id'          => $request->inventory_id,
                ]);
                return redirect()->back()->with('success', 'Product With name <b>'.strtoupper($request->product_name).' </b> Successfully Registered  : ');
    }
    
    public function deleteUnsubmittedInventory($id, $status)
    {
            Inventory::find($id)->delete();
            ProductsInventory::where('inventory_id', $id)->delete();
            return redirect()->back()->with('success', 'All related inventory data deleted successfully!');
    }
    public function deleteUnsubmittedProduct($id, $status)
    {
            ProductsInventory::find($id)->delete();
            return redirect()->back()->with('success', 'Product Deleted!');
    }
    public function register(Request $request)
    {   
		// 'expire_date',
        $request->validate([
            'product_name' => 'required|string|max:255',
            'qty' => 'required|integer',
            'category' => 'required|string|max:255',
            'barcode' => 'required|string|max:100|unique:products,barcode',
            'purchasing_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);
        
        if(Auth::user()->role == 'ADMIN')
        {
            $user = Product::create(   
                [
                    'product_name'          => $request->product_name,
                    'qty'                   => $request->qty,
                    'qty_remained'          => $request->qty,
                    'unit_of_measuer'       => $request->unit_of_measuer,
                    'category'              => $request->category,
                    'barcode'               => $request->barcode,
                    'purchasing_price'      => $request->purchasing_price,
                    'selling_price'         => $request->selling_price,
                    'description'           => $request->description,
                    'store_id'              => $request->store_id,
                    'company_id'            => intval(Auth::user()->company_id),
                    'created_by'            => intval(Auth::user()->id),
                    'status'                => 'Active',
                    'reg_at'                => date('Y-m-d H:i:s'),
                    'inventory_id'          => $request->inventory_id,
                ]);
        }else
        {
            $user = Product::create(   
                [
                    'product_name'          => $request->product_name,
                    'qty'                   => $request->qty,
                    'qty_remained'          => $request->qty,
                    'unit_of_measuer'       => $request->unit_of_measuer,
                    'category'              => $request->category,
                    'barcode'               => $request->barcode,
                    'purchasing_price'      => $request->purchasing_price,
                    'selling_price'         => $request->selling_price,
                    'description'           => $request->description,
                    'store_id'              => $request->store_id,
                    'company_id'            => intval(Auth::user()->company_id),
                    'created_by'            => intval(Auth::user()->id),
                    'status'                => 'new_product',
                    'reg_at'                => date('Y-m-d H:i:s'),
                    'inventory_id'          => $request->inventory_id,
                ]);
        }
        
            return redirect()->back()->with('success', 'Product With name <b>'.strtoupper($request->product_name).' </b> Successfully Registered  : ');
    }
    public function registerNewExpenses(Request $request)
    {   
		 // Check for duplicates based on expense_id and inventory_id
    $existingRecord = ExpensesRecord::where('expense_id', $request->expense_id)
        ->where('inventory_id', $request->inventory_id)
        ->first();

    if ($existingRecord) {
        return redirect()->back()->with('error', 'Duplicate expense record already exists for this inventory and expense type.');
    }
        $user = ExpensesRecord::create(   
            [
                'expense_id'          => $request->expense_id,
                'amount_used'          => $request->amount_used,
                'desr'                 => $request->desr,
                'company_id'            => intval(Auth::user()->company_id),
                'reg_by'            => intval(Auth::user()->id),
                'status'                => 'Active',
                'reg_at'                => date('Y-m-d H:i:s'),
                'inventory_id'          => $request->inventory_id,
                
            ]);
            return redirect()->back()->with('success', 'Expenses Recorded  successful.');    

        }

        public static function storeName($storeId)
    {
        return Store::where('id', $storeId)->value('name') ?? 'N/A';
    }

}
