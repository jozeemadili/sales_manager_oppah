<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\EditedProduct;
use App\Models\HotelInvoice;
use App\Models\HotelInvoiceItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\ProductTransferDetail;
use App\Models\Room;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\TIRAClient\Scripts\Classes\Utils;


use App\Models\InvoicePaymentDetail;



class InvoiceController extends Controller
{
    public function download($id) 
    {
        $Utils = new Utils();  
        $Invoice = Invoice::with('invoice_items')->find($id);
        if($Invoice != null)
        {
           
            $qrcode = $Utils->getQrcode("https://jaja.co.tz/".$id, 50, 'svg');
            $svgContent = $Utils->svgToBase64($qrcode);
            $pdf       = Pdf::loadView('admin.sales_management.invoice-download', ['quotation' => $Invoice,'qrcode'=>$svgContent]);
            // return View('admin.sales_management.invoice-download', ['quotation' => $Invoice]);
            // return $pdf;
            return $pdf->download(strtolower(str_replace(' ','',$Invoice->id)).'_'.$Invoice->id.'.pdf');
        }

        return array('responseCode' => '404', 'message' => 'Invoice not Found.');
    }
    public function downloadHotelInvoice($id) 
    {
        $Utils = new Utils();  
        $Invoice = HotelInvoice::with('hotel_invoice_items')->find($id);
        if($Invoice != null)
        {
           
            $qrcode = $Utils->getQrcode("https://jaja.co.tz/".$id, 50, 'svg');
            $svgContent = $Utils->svgToBase64($qrcode);
            $pdf       = Pdf::loadView('admin.sales_management.hotel-invoice-download', ['quotation' => $Invoice,'qrcode'=>$svgContent]);
            // return View('admin.sales_management.invoice-download', ['quotation' => $Invoice]);
            // return $pdf;
            return $pdf->download(strtolower(str_replace(' ','',$Invoice->id)).'_'.$Invoice->id.'.pdf');
        }

        return array('responseCode' => '404', 'message' => 'Invoice not Found.');
    }
    public function invoicePreview($id) 
    {

        $Invoice = Invoice::with('invoice_items')->find($id);
        if($Invoice != null)
        {
            // $pdf       = Pdf::loadView('admin.sales_management.invoice-download', ['quotation' => $Invoice]);
            return View('admin.sales_management.invoice-preview', ['quotation' => $Invoice,'invoice_id'=>$id]);
            // return $pdf;
            // return $pdf->download(strtolower(str_replace(' ','',$Invoice->id)).'_'.$Invoice->id.'.pdf');
        }

        return array('responseCode' => '404', 'message' => 'Invoice not Found.');
    }
    public function invoicePreviewHotel($id) 
    {

        $Invoice = HotelInvoice::with('hotel_invoice_items')->find($id);
        if($Invoice != null)
        {
            // $pdf       = Pdf::loadView('admin.sales_management.invoice-download', ['quotation' => $Invoice]);
            return View('admin.sales_management.invoice-preview-hotel', ['quotation' => $Invoice,'invoice_id'=>$id]);
            // return $pdf;
            // return $pdf->download(strtolower(str_replace(' ','',$Invoice->id)).'_'.$Invoice->id.'.pdf');
        }

        return array('responseCode' => '404', 'message' => 'Invoice not Found.');
    }
    public function EdititemInvoice(Request $request)
{   
    // 1️⃣ Find the invoice item
    $InvoiceItem = InvoiceItem::find($request->item_id);

    if (!$InvoiceItem) {
        return redirect()->back()->with('error', 'Invoice item not found.');
    }

    // 2️⃣ Update the invoice item with discount
    $InvoiceItem->price = $request->discount_amount; 
    $InvoiceItem->discount = 'yes';
    $InvoiceItem->discounte_by = intval(Auth::user()->id);
    $InvoiceItem->date_discounted = now();
    $InvoiceItem->save();

    // 3️⃣ Get the related invoice
    $invoice = $InvoiceItem->invoice;

    // 4️⃣ Recalculate totals
    $total_invoice_amount = $invoice->invoice_items()->sum(\DB::raw('qty * price'));

    // 5️⃣ Update invoice financial fields
    // If there are payments made already, keep them in place
    $amount_paid = $invoice->amount_paid ?? 0;
    $amount_remained = $total_invoice_amount - $amount_paid;

    // 6️⃣ Save updated invoice totals
    $invoice->update([
        'total_invoice_amount' => $total_invoice_amount,
        'amount_paid' => $amount_paid,
        'amount_remained' => $amount_remained,
    ]);

    return redirect()->back()->with('success', 'Discount applied and invoice updated successfully.');
}

    public function EdititemInvoiceHotel(Request $request)
    {   
        $InvoiceItem=HotelInvoiceItem::find($request->item_id);
        $InvoiceItem->price = $request->discount_amount; 
        $InvoiceItem->discount = 'yes';
        $InvoiceItem->discounte_by = intval(Auth::user()->id);
        $InvoiceItem->date_discounted = date('Y-m-d H:i:s');
        $InvoiceItem->save();
        return redirect()->back()->with('success', 'Discounted successful.');
          
    }
    public function editUserStore(Request $request)
    {   
        $InvoiceItem=User::find($request->user_id);
        $InvoiceItem->office_location = $request->store_id; 
        $InvoiceItem->save();
        return redirect()->back()->with('success', 'successful.');
          
    }
    public function removeItemFromInvoice(Request $request)
    {   
        $InvoiceItem=InvoiceItem::find($request->item_id);
        $InvoiceItem->qty =  $InvoiceItem->qty - $request->qty_to_return; 
        $InvoiceItem->save();

        // $InvoiceItem=InvoiceItem::find($request->item_id);
        $editedProduct = new EditedProduct();
        $editedProduct->product_id = $InvoiceItem->product_id;
        $editedProduct->product_name = $InvoiceItem->Product->product_name;
        $editedProduct->qty = $InvoiceItem->Product->qty;
        $editedProduct->category = $InvoiceItem->Product->category;
        $editedProduct->purchasing_price =  $InvoiceItem->Product->purchasing_price;
        $editedProduct->selling_price =  $InvoiceItem->Product->selling_price;
        $editedProduct->description =  $InvoiceItem->Product->description;
        $editedProduct->unit_of_measuer =$InvoiceItem->Product->unit_of_measuer;
        $editedProduct->edited_by = auth()->user()->id; 
        $editedProduct->reason_for_editing = $request->reason_for_retuning;
        $editedProduct->date_edited = Carbon::now();
        $editedProduct->save();

        //update quantity remained 
        $product_edit = Product::find($InvoiceItem->product_id);
        $product_edit->status = "edited"; 
        $product_edit->qty_remained = $product_edit->qty_remained+$request->qty_to_return;
        $product_edit->save();

        // product_id
        return redirect()->back()->with('success', 'Removed successful.');
          
    }
    public function EdititemInvoiceQty(Request $request)
{
    // 1️⃣ Validate request
    // $request->validate([
    //     'item_id' => 'required|integer|exists:invoice_items,id',
    //     'product_id' => 'required|integer|exists:products,id',
    //     'new_qty' => 'required|numeric|min:0.01',
    // ]);

    // 2️⃣ Check product stock availability
    $item = Product::where('id', $request->product_id)
        ->where('qty_remained', '>=', $request->new_qty)
        ->first();

    if (!$item) {
        return redirect()->back()->with('error', 'Insufficient stock available. Check remaining quantity or adjust quantity!');
    }

    // 3️⃣ Update the invoice item quantity
    $InvoiceItem = InvoiceItem::find($request->item_id);
    $InvoiceItem->qty = $request->new_qty;
    $InvoiceItem->save();

    // 4️⃣ Fetch related invoice
    $invoice = $InvoiceItem->invoice;

    // 5️⃣ Recalculate invoice totals (sum of all invoice items)
    $total_invoice_amount = $invoice->invoice_items()->sum(\DB::raw('qty * price'));

    // 6️⃣ Keep amount_paid, recalc remained
    $amount_paid = $invoice->amount_paid ?? 0;
    $amount_remained = $total_invoice_amount - $amount_paid;

    // 7️⃣ Update invoice record
    $invoice->update([
        'total_invoice_amount' => $total_invoice_amount,
        'amount_paid' => $amount_paid,
        'amount_remained' => $amount_remained,
    ]);

    return redirect()->back()->with('success', 'Quantity changed and invoice updated successfully.');
}


    public function EditDateInvoiceHotel(Request $request)
    {   
        $HotelInvoiceItem=HotelInvoiceItem::find($request->item_id);
        $HotelInvoiceItem->start_date = $request->start_date; 
        $HotelInvoiceItem->end_date = $request->end_date; 
        $HotelInvoiceItem->save();
        return redirect()->back()->with('success', 'Dated Changed successful.');
          
    }
    
    // public function invoiceStatusUpdate($id) 
    // {

    //     $Invoice = Invoice::find($id);

    // // Check if invoice is already confirmed
    // if ($Invoice && $Invoice->status !== "Confirmed") {
    //     // $Invoice->status = "Confirmed"; 
    //     // $Invoice->invoice_date = Carbon::now();
    //     // $Invoice->save();

    //     // $Invoices = Invoice::find($id);
    //     $InvoiceItem=InvoiceItem::where('invoice_id',$id)->get();
    //     // dd($InvoiceItem);
    //     foreach ($InvoiceItem as $items) 
    //     {
    //         dump("product_id ".$items->product_id);
    //         // dd($items);
    //         // $product = Product::find($items->product_id); // Assuming 'product_id' exists in the Sale table

    //         // if ($product) {
    //         //     // Update qty_sold and qty_remained
    //         //     $product->invoiced_qty = $items->qty; // Assuming $sale has a 'quantity' field
    //         //     $product->qty_remained = $product->qty_remained - $items->qty;

    //         //     // Save the updated product data
    //         //     $product->save();
            
    //         // }
    //         // return redirect()->back()->with('success', 'Invoice marked as Confermed successful.');
    //     }
    //     dd('done');
    
    // }else{
    //     return redirect()->back()->with('success', 'Duplicate request');
    // }

           
    // }
    public function invoiceStatusUpdate($id) 
{
    $invoice = Invoice::find($id);

    // Check if invoice is already confirmed
    if ($invoice && $invoice->status !== "Confirmed") {

        // Update invoice status and date
        $invoice->status = "Confirmed"; 
        $invoice->invoice_date = Carbon::now();
        $invoice->save();

        // Get all items for this invoice
        $invoiceItems = InvoiceItem::where('invoice_id', $id)->get();

        foreach ($invoiceItems as $item) {
            $product = Product::find($item->product_id);

            if ($product) {
                // Update product stock info
                $product->invoiced_qty = $item->qty;
                $product->qty_remained -= $item->qty;
                $product->save();
            }
        }

        return redirect()->back()->with('success', 'Invoice marked as Confirmed successfully.');

    } else {
        return redirect()->back()->with('success', 'Duplicate request');
    }
}
    public function invoiceStatusUpdateHotel($id) 
    {

            $Invoice = HotelInvoice::find($id);
            $Invoice->status = "Confirmed"; 
            $Invoice->invoice_date = Carbon::now();
            $Invoice->save();

            // $Invoices = HotelInvoice::find($id);
            // $InvoiceItem=HotelInvoiceItem::where('invoice_id',$id)->get();
            // foreach ($InvoiceItem as $items) 
            // {
            //     $product = Product::find($items->product_id); // Assuming 'product_id' exists in the Sale table
        
            //     if ($product) {
            //         // Update qty_sold and qty_remained
            //         $product->invoiced_qty = $items->qty; // Assuming $sale has a 'quantity' field
            //         $product->qty_remained = $product->qty_remained - $items->qty;
        
            //         // Save the updated product data
            //         $product->save();
                   
            //     }
            // }
            return redirect()->back()->with('success', 'Invoice marked as Confermed successful.');

    }
  
    
    public function invoiceStatusUpdatePaid(Request $request) 
    {
        // dd($request->item_id);
        $id=$request->item_id;
        $Invoice = Invoice::find($id);
    
        if (!$Invoice) {
            return redirect()->back()->with('error', 'Invoice not found.');
        }
    
        // Get the amount entered by the user
        $newAmount = floatval($request->input('discount_amount', 0));
    
        // Check if the amount exceeds remaining amount
        if ($newAmount > $Invoice->amount_remained) {
            return redirect()->back()->with('error', 'Entered amount exceeds remaining amount.');
        }
    
        // Update invoice totals
        $Invoice->amount_paid += $newAmount;
        $Invoice->amount_remained -= $newAmount;
    
        // Update invoice status based on remaining amount
        if ($Invoice->amount_remained <= 0) {
            $Invoice->status = 'Paid';
            $Invoice->amount_remained = 0; // avoid negative
            $Invoice->paid_by = auth()->user()->first_name;
            $Invoice->date_paid = Carbon::now();
        } else {
            $Invoice->status = 'Partial_Paid';
        }
    
        $Invoice->save();
    
        // Create a new record in InvoicePaymentDetail
        InvoicePaymentDetail::create([
            'invoice_no' => $Invoice->id,
            'amount_submitted' => $newAmount,
            'date_payed' => Carbon::now(),
            'status' => 'Paid',
            'payer_id' => auth()->user()->first_name, // full name
            'receipt_number' => 'REC-' . time() . '-' . $Invoice->id,
            'channel' => 'Manual',
        ]);
    
        // Update Invoice Items if fully paid
        if ($Invoice->status === 'Paid') {
            $InvoiceItems = InvoiceItem::where('invoice_id', $id)->get();
            foreach ($InvoiceItems as $item) {
                $item->status = 'Paid';
                $item->paid_by = auth()->user()->first_name;
                $item->date_paid = Carbon::now();
                $item->save();
            }
        }
    
        return redirect()->back()->with('success', 'Invoice updated and payment recorded successfully.');
    }
    
    public function invoiceStatusUpdatePaidHotel($id) 
    {

            $Invoice = HotelInvoice::find($id);
            $Invoice->status = "Paid";
            $Invoice->paid_by = auth()->user()->first_name;
            $Invoice->date_paid = Carbon::now();
            $Invoice->save();

            $Invoices = HotelInvoice::find($id);
            $InvoiceItem=HotelInvoiceItem::where('invoice_id',$id)->get();

            foreach ($InvoiceItem as $items) 
            {
                $items->status = 'Paid';
                $items->paid_by = auth()->user()->first_name;
                $items->date_paid = Carbon::now();
                $items->save();

                $Room = Room::find($items->room_id);
                $Room->occupied = "no";
                $Room->save();

            }

           
            return redirect()->back()->with('success', 'Invoice marked as Paid successful.');

    }

    public function pendingInvoices(Request $request)
    {
        $query = Invoice::with(['Customer', 'User', 'invoice_items']);


        if ($request->filled('customer_name')) {
            $query->whereHas('Customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer_name . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('control_no')) {
            $query->where('control_no', 'like', '%' . $request->control_no . '%');
        }

        // if ($request->filled('start_date') && $request->filled('end_date')) {
        //     $query->whereBetween('invoice_date', [$request->start_date, $request->end_date]);
        // }
        if ($request->filled('start_date') && $request->filled('end_date')) {
                $start = Carbon::parse($request->start_date)->setTime(0, 1);
                $end = Carbon::parse($request->end_date)->setTime(23, 59);
            
                $query->whereBetween('invoice_date', [$start, $end]);
            }

        if ($request->filled('start_month') && $request->filled('end_month')) {
            $query->whereYear('invoice_date', date('Y', strtotime($request->start_month)))
                ->whereMonth('invoice_date', '>=', date('m', strtotime($request->start_month)))
                ->whereMonth('invoice_date', '<=', date('m', strtotime($request->end_month)));
        }

        // Fetch filtered data
        $Invoice = $query->orderBy('invoice_date', 'desc')->paginate(50);

        return view('admin.sales_management.invoice-reports', compact('Invoice'));
    }

//     public function pendingInvoices(Request $request)
//     {
// //         $query = Invoice::with(['Customer', 'User', 'invoice_items']);


// //         if ($request->filled('customer_name')) {
// //             $query->whereHas('Customer', function ($q) use ($request) {
// //                 $q->where('name', 'like', '%' . $request->customer_name . '%');
// //             });
// //         }

// //         if ($request->filled('status')) {
// //             $query->where('status', $request->status);
// //         }

// //         if ($request->filled('control_no')) {
// //             $query->where('control_no', 'like', '%' . $request->control_no . '%');
// //         }

// //         if ($request->filled('start_date') && $request->filled('end_date')) {
// //             $query->whereBetween('invoice_date', [$request->start_date, $request->end_date]);
// //         }

// //         if ($request->filled('start_month') && $request->filled('end_month')) {
// //             $query->whereYear('invoice_date', date('Y', strtotime($request->start_month)))
// //                 ->whereMonth('invoice_date', '>=', date('m', strtotime($request->start_month)))
// //                 ->whereMonth('invoice_date', '<=', date('m', strtotime($request->end_month)));
// //         }

// //         // Clone the query for totals BEFORE pagination
// // $totalsQuery = (clone $query)->with('invoice_items')->get();

// // $totalQty = 0;
// // $totalAmount = 0;

// // foreach ($totalsQuery as $invoice) {
// //     foreach ($invoice->invoice_items as $item) {
// //         $totalQty += $item->qty;
// //         $totalAmount += $item->qty * $item->price;
// //     }
// // }

// //         // Fetch filtered data
// //         $Invoice = $query->orderBy('invoice_date', 'desc')->paginate(10);

// //         // return view('admin.sales_management.invoice-reports', compact('Invoice'));
        
       
// //         return view('admin.sales_management.invoice-reports', compact('Invoice', 'totalQty', 'totalAmount'));
// $query = Invoice::with(['Customer', 'User', 'invoice_items']);

// // Apply filters
// if ($request->filled('customer_name')) {
//     $query->whereHas('Customer', function ($q) use ($request) {
//         $q->where('name', 'like', '%' . $request->customer_name . '%');
//     });
// }

// if ($request->filled('status')) {
//     $query->where('status', $request->status);
// }

// if ($request->filled('control_no')) {
//     $query->where('control_no', 'like', '%' . $request->control_no . '%');
// }

// if ($request->filled('start_date') && $request->filled('end_date')) {
//     $query->whereBetween('invoice_date', [$request->start_date, $request->end_date]);
// }

// if ($request->filled('start_month') && $request->filled('end_month')) {
//     $query->whereYear('invoice_date', date('Y', strtotime($request->start_month)))
//         ->whereMonth('invoice_date', '>=', date('m', strtotime($request->start_month)))
//         ->whereMonth('invoice_date', '<=', date('m', strtotime($request->end_month)));
// }

// // Clone the query for totals BEFORE pagination
// $totalsQuery = (clone $query)->with('invoice_items')->get();

// $totalQty = 0;
// $totalAmount = 0;

// foreach ($totalsQuery as $invoice) {
//     foreach ($invoice->invoice_items as $item) {
//         $totalQty += $item->qty;
//         $totalAmount += $item->qty * $item->price;
//     }
// }

// Now paginate actual query
// $Invoice = $query->orderBy('invoice_date', 'desc')->paginate(50);

// return view('admin.sales_management.invoice-reports', compact('Invoice', 'totalQty', 'totalAmount'));
//     }

    public function downloadCSV(Request $request)
    {
        $query = Invoice::with(['Customer', 'User', 'invoice_items']);

        // Apply filters (same as pendingInvoices method)
        if ($request->filled('invoice_no')) {
            $query->where('id', 'like', '%' . $request->invoice_no . '%');
        }
        
        if ($request->filled('customer_name')) {
            $query->whereHas('Customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer_name . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('control_no')) {
            $query->where('control_no', 'like', '%' . $request->control_no . '%');
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('invoice_date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('start_month') && $request->filled('end_month')) {
            $query->whereYear('invoice_date', date('Y', strtotime($request->start_month)))
                ->whereMonth('invoice_date', '>=', date('m', strtotime($request->start_month)))
                ->whereMonth('invoice_date', '<=', date('m', strtotime($request->end_month)));
        }

        // Fetch data
        $invoices = $query->get();

        // Create CSV file
        $csvFileName = 'invoices.csv';
        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
        ];

        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Invoice No', 'Customer', 'Invoice Date', 'Total Amount', 'Status']);

        foreach ($invoices as $invoice) {
            $totalAmount = $invoice->invoice_items->sum(fn($item) => $item->qty * $item->price);
            fputcsv($handle, [
                'ND000' . $invoice->id . '/025',
                $invoice->Customer->name,
                $invoice->invoice_date,
                number_format($totalAmount, 2),
                $invoice->status,
            ]);
        }

        fclose($handle);

        return response()->streamDownload(function () use ($handle) {
            rewind($handle);
            fpassthru($handle);
        }, $csvFileName, $headers);
    }

    

}
