<?php $__env->startSection('title'); ?>
<?php echo e(ucfirst(str_replace('-',' ',Route::currentRouteName()))); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('css'); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
  <?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('breadcrumb_title'); ?>
      <h3><?php echo e(ucfirst(str_replace('-',' ',Route::currentRouteName()))); ?></h3>
    <?php $__env->endSlot(); ?>

    
    <?php $__env->slot('breadcrumb_action_buttons'); ?>
    <!-- <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#searchModal">Search <i class="icofont icofont-search-alt-1"></i></button></li> -->
      

    <?php $__env->endSlot(); ?>
    <li class="breadcrumb-item"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[0])); ?></li>
    <li class="breadcrumb-item active"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[1])); ?></li>
  <?php echo $__env->renderComponent(); ?>
  
  <div class="container-fluid">
  <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                    <i class="icon-info-alt txt-danger"></i>
                        <?php echo e($error); ?>

                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  
                  <?php if($message = Session::get('success')): ?>
                    <div class="alert alert-success outline alert-dismissible fade show" role="alert">
                    <i class="icofont icofont-check-circled"></i>
                        <?php echo $message; ?>

                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                    <br />
					<?php endif; ?>
      <div class="row">
      <!DOCTYPE html>
<html lang="en">
    <head>
        
        <title>safeafrica</title>
        <style>
     #invoice {
  padding: 30px;
}

.invoice {
  position: relative;
  background-color: #FFF;
  min-height: 680px;
  padding: 15px;
}

.invoice header {
  padding: 10px 0;
  margin-bottom: 20px;
  border-bottom: 1px solid #223673;
}

.invoice .company-details {
  text-align: left;
}

.invoice .company-details .name {
  margin-top: 0;
  margin-bottom: 0;
}

.invoice .contacts {
  margin-bottom: 20px;
}

.invoice .invoice-to {
  text-align: left;
}

.qrcode {
  text-align: right;
}

.invoice .invoice-to .to {
  margin-top: 0;
  margin-bottom: 0;
}

.invoice .invoice-details {
  text-align: right;
}

.invoice .invoice-details .invoice-id {
  margin-top: 0;
  color: #223673;
}

.invoice main {
  padding-bottom: 50px;
}

.invoice main .thanks {
  margin-top: -110px;
  font-size: 2em;
  margin-bottom: 10px;
}

.invoice main .notices {
  padding-left: 6px;
  border-left: 6px solid #223673;
}
.invoice main .notice2 {
  padding-right: 6px;
  border-right: 6px solid #223673;
}

.invoice main .notices .notice {
  font-size: 1.2em;
}

.invoice table {
  width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
  margin-bottom: 20px;
}

.invoice table td,
.invoice table th {
  padding: 15px;
  /* border-bottom: 1px solid #fff; */
  background: none; /* Background removed */
}

.invoice table th {
  white-space: nowrap;
  font-weight: 400;
  font-size: 16px;
}

.invoice table td h3 {
  margin: 0;
  font-weight: 400;
  font-size: 1em;
}

.invoice table .qty,
.invoice table .total,
.invoice table .unit {
  text-align: center;
  font-size: 1.2em;
}

.invoice table .no {
  font-size: 1.2em;
}

.invoice table .unit {
  background: none; /* Background removed */
}

.invoice table .total {
  color: #fff;
}

.invoice table tbody tr:last-child td {
  /* border: none; */
}

.invoice table tfoot td {
  background: none; /* Background removed */
  border-bottom: none;
  white-space: nowrap;
  text-align: right;
  padding: 10px 20px;
  font-size: 1.2em;
  border-top: 1px solid #aaa;
}

.invoice table tfoot tr:first-child td {
  /* border-top: none; */
}

.invoice table tfoot tr:last-child td {
  font-size: 1.4em;
}

.invoice table tfoot tr td:first-child {
  /* border: none; */
}

.invoice footer {
  width: 100%;
  text-align: center;
  font-size: 12px;
  color: #777;
  border-top: 1px solid #aaa;
  padding: 8px 0;
}
.invoice .company-details-heading {
        text-align: center;
        font-size: 15px
        
        }
.invoice footer {
  position: absolute;
  bottom: 10px;
}

.invoice>div:last-child {
}

.th {
  background-color: white;
  color: black;
}

.page-break {
  page-break-after: always;
}

                        </style>
    </head>
    <body style="margin: 20px auto;">
      <div id="invoice">
        <div class="invoice overflow-auto">
            <div style="min-width: 600px">
                <header>
                    <div class="row">
                        <div class="col company-details">
                        <button class="btn btn-primary" onclick="window.history.back();">
    Go Back
</button>
                            <table>
                                <tr>
                                    <td>
                                    <img src="<?php echo e(asset('/assets/images/logo/'.strtolower($quotation->company->short_form)).'.png'); ?>" data-holder-rendered="true" width="20%" />
                                    </td>
                                    <td>
                                    <div><?php echo e(strtoupper($quotation->company->name)); ?> </div>
                                    <div> +255<?php echo e($quotation->company->phone_number); ?>/+255762912665 | <?php echo e($quotation->company->email_address); ?></div>
                                    <div>P.O.BOX <?php echo e($quotation->company->postal_address); ?> </div>
                                   
                                    </td>
                                </tr>
                            </table>
                        </div>
  
                    </div>
                </header>

                <main>
                <div class="col company-details-heading">

                  <?php if($quotation->status == 'Pending'): ?>
                  <div class="text-gray-light"><b> PROFOMAL INVOICE </b></div>
                  <?php elseif($quotation->status == 'Confirmed'): ?>
                  <div class="text-gray-light"><b> INVOICE </b></div>
                  <?php elseif($quotation->status == 'Paid'): ?>
                  <div class="text-gray-light"><b> INVOICE </b></div>
                  <?php else: ?>
                  <div class="text-gray-light"><b> INVOICE </b></div>
                  <?php endif; ?>
                     
                </div>
                <div class="col invoice-details">
                <div><b>Invoice No : </b> OP000<?php echo e($quotation->id); ?>/025 </div>
                <div><b>Date : </b> <?php echo e($quotation->invoice_date->format('d M Y')); ?> </div>
                </div>

                <div class="row contacts">
               

                        <div class="col invoice-to">
                            <div class="text-gray-light"> THE BUYER (INVOICE TO) :</div>
                            <h6 class="to"> <?php echo e(strtoupper($quotation->customer->name)); ?>  </h6>
                            <div class="address">+255<?php echo e($quotation->customer->phone); ?> | <?php echo e($quotation->customer->email); ?></div>
                            <div>TIN: <?php echo e($quotation->customer->tin); ?> VRN : <?php echo e($quotation->customer->tin); ?> </div>
                            <div>P.O.BOX <?php echo e($quotation->customer->physical_addres); ?></div>
                        </div>
<br/>

                        <table border="1" cellspacing="0" cellpadding="0">
                        <thead >
                            <tr>
                                <th style="background-color: <?php echo e($quotation->company->color); ?>; color:white;">#</th>
                                <th style="background-color: <?php echo e($quotation->company->color); ?>; color:white;">PRODUCT NAME</th>
                                <th style="background-color: <?php echo e($quotation->company->color); ?>; color:white;">BARCODE</th>
                                <th style="background-color: <?php echo e($quotation->company->color); ?>; color:white;">QUANTITY</th>
                                <th style="background-color: <?php echo e($quotation->company->color); ?>; color:white;">SUB TOTAL</th>
                                <th style="background-color: <?php echo e($quotation->company->color); ?>; color:white;">TOTAL</th>
                                <th style="background-color: <?php echo e($quotation->company->color); ?>; color:white;">ACTION</th>
                            </tr>
                           
                           
                        </thead>
                        <tbody>
        <?php
            $total = 0;
        ?>

        
        <tbody>
        <?php
            $total = 0;
        ?>

        <?php $__currentLoopData = $quotation->invoice_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $subTotal = $item->price * $item->qty;
                $total += $subTotal;
            ?>
            <tr>
                <td><?php echo e($loop->index + 1); ?>.</td>
                <td><?php echo e($item->Product->product_name); ?></td>
                <td><?php echo e($item->Product->barcode); ?></td> 
                <td><?php echo e($item->qty); ?></td>
                <td><?php echo e(number_format($item->price, 2)); ?> TZS</td> 
                <td><?php echo e(number_format($subTotal, 2)); ?> TZS</td>
                <td>
                <?php if($quotation->status == 'Pending'): ?>
                 <a class='btn btn-outline-info btn-xs'  data-bs-toggle="modal"  data-bs-target="#editqtyModal<?php echo e($item->id); ?>">Edit Qnty</a>
                  &nbsp;&nbsp;&nbsp;
                  <a  class="btn btn-primary btn-xs" data-bs-toggle="modal" data-bs-target="#approvalModal<?php echo e($item->id); ?>"><i class='fa fa-tags'></i> Discount</a>
                  <?php else: ?>
                  <!-- <a class='btn btn-outline-secondary btn-xs'>Can not Edit, Invoice is Confemed By Customer!</a> -->
                  <a  class="btn btn-info btn-xs" data-bs-toggle="modal" data-bs-target="#returnToStock<?php echo e($item->id); ?>"> Return To Stock</a>
                  <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      
    </tbody>
    <tfoot>
      <tr>
                          
                            <td colspan="5">Subtotal</td>
                              <td><?php echo e(number_format($total,2,'.',',')); ?> TZS</td>
                            </tr>
                            <tr>
                              <td></td>
                              <td colspan="4">Tax %</td>
                              <td>0</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan="4">GRAND TOTAL</td>
                                <td><?php echo e(number_format($total,2,'.',',')); ?> TZS</td>
                            </tr>
                        </tfoot>
                    </table>
                        
                    </div>
                    <table>
                        <tr style="font-size: 9px; ">
                            <td>
                            <div class="notices">
                        <div>NOTICE:</div>
                        <ul>
                          <li> <div class="notice">All Figures above are in Tanzania Shillings (TZS)</div></li>
                          
                        </ul>
                       
                        
                    </div>
                  
                            </td>
                            <td style="text-align: right;">
    <div class="notice2">
    <div class="notice"><i>www.oppah01.co.tz</i></div>
    <div class="notice"><i>info@oppah01.co.tz</i></div>
    <div class="notice"><i>Dar Es Dalaam, Tanzania</i></div>
        
    </div>
</td>
                        </tr>
                      </table>
                   
                </main>
                <footer>
                    Smart Generation in Smart Bussines
                </footer>
            </div>
            <div></div>
        </div>
    </div>
    

 
    </body>
</html>
       
      </div>
  </div>




  <?php $__env->startPush('scripts'); ?>
  <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php $__currentLoopData = $quotation->invoice_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="editqtyModal<?php echo e($item->id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Edit Quantity For Item : <?php echo e($item->Product->product_name); ?></h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                <form method="post" action="<?php echo e(Route('edit-item-invoice-qty')); ?>">
                    <?php echo csrf_field(); ?>
                <div class="form-group">

                                <label class="col-form-label" > Enter New Quantity </label>
                                <input class="form-control" type="text" value="<?php echo e(old('branch_name')); ?>" required  name="new_qty">
                                <i>Previous Quantity was <?php echo e(number_format($item->qty, 2)); ?></i>
                                <input type="hidden" name="item_id"value="<?php echo e($item->id); ?>">
                                <input type="hidden" name="product_id"value="<?php echo e($item->product_id); ?>">
                                
                                <input type="hidden" name="invoice_id"value="<?php echo e($invoice_id); ?>">
                    </div>
                    <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm & Edit Quantity</button>
                </form>
            </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__currentLoopData = $quotation->invoice_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="returnToStock<?php echo e($item->id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"> Return Item : <?php echo e($item->Product->product_name); ?> To stock</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                <form method="post" action="<?php echo e(Route('remove-item-from-invoice')); ?>">
                    <?php echo csrf_field(); ?>
                <div class="form-group">

                                <label class="col-form-label" > Quantity To Return To Stock </label>
                                <input class="form-control" type="number" required value="<?php echo e($item->qty); ?>" required  name="qty_to_return" min="1" max="<?php echo e($item->qty); ?>">

                                <i>Sold Quantity was <?php echo e(number_format($item->qty, 2)); ?></i>
                                <input type="hidden" name="item_id"value="<?php echo e($item->id); ?>">
                                <input type="hidden" name="invoice_id"value="<?php echo e($invoice_id); ?>">
                                <div class="form-group">
                            <label class="col-form-label">Reason For Returning</label>
                            <textarea class="form-control" name="reason_for_retuning" rows="3" placeholder="Briefly describe the product..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm & Discount</button>
                </form>
            </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__currentLoopData = $quotation->invoice_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="approvalModal<?php echo e($item->id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Enter Discount Amount  For Item : <?php echo e($item->Product->product_name); ?></h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                <form method="post" action="<?php echo e(Route('edit-item-invoice-price')); ?>">
                    <?php echo csrf_field(); ?>
                <div class="form-group">

                                <label class="col-form-label" > Enter New Amount </label>
                                <input class="form-control" type="text" value="<?php echo e(old('branch_name')); ?>" required  name="discount_amount">
                                <i>Previous Amount was <?php echo e(number_format($item->price, 2)); ?> TZS</i>
                                <input type="hidden" name="item_id"value="<?php echo e($item->id); ?>">
                                <input type="hidden" name="invoice_id"value="<?php echo e($invoice_id); ?>">
                    </div>
                    <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm & Discount</button>
                </form>
            </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


 

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/admin/sales_management/invoice-preview.blade.php ENDPATH**/ ?>