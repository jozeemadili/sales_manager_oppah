

<div class="row">
    <div class="col-lg-4">
    <div class="form-group">
               
                <div class="form-group">
    <label for="startDate">Enter Qauantity</label>
    <input wire:model="quantity_new" type="number" id="quantity" value="1"class="form-control">
</div>
<label>Choose Product To Be Sold To <b><?php echo e($Customers_details->name); ?></b> </label>
                <input class="form-control" wire:model="customerquery" type="text" placeholder="Search by, Product Name or Barcode" aria-label="Search by, Product Name or Barcode">
                <div class="list-group">
                 
                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($customer->qty_remained < 5): ?>
                    <a wire:click="selectCustomer(<?php echo e($customer); ?>)" class="list-group-item list-group-item-action" href="javascript:void(0)" data-bs-original-title="">
                        <div class="d-flex w-100 justify-content-between">
                            <small><b><?php echo e(strtoupper($customer->product_name)); ?> | <?php echo e(strtoupper($customer->barcode)); ?></b></small>
                        <small class="text-muted"><font color='red'><i>Limited stock: Only <?php echo e($customer->qty_remained); ?> left!</i></font> <span class="badge badge-danger rounded-pill counter"><?php echo e($customer->qty_remained); ?></span></small>
                        </div>
                    <small class="text-muted"><?php echo e(number_format($customer->selling_price, 2)); ?> TZS | <?php echo e(strtoupper($customer->stores_tuli->name)); ?> | 
                    <?php if($customer->expire_date == null || $customer->expire_date == 'N/A'): ?>
                        <i>Expiry Status:</i> N/A
                    <?php else: ?>
                        <i>Expiry Status:</i> <?php echo e(\Carbon\Carbon::parse($customer->expire_date)->format('d-M-Y')); ?>

                    <?php endif; ?> 
                </small>

                   
                    </a>
            <?php elseif($customer->qty_remained == 0): ?>

                    <a class="list-group-item list-group-item-action" href="#" data-bs-original-title="">
                        <div class="d-flex w-100 justify-content-between">
                            <small><b><?php echo e(strtoupper($customer->product_name)); ?> | <?php echo e(strtoupper($customer->barcode)); ?></b></small>
                        <small class="text-muted">Remained <span class="badge badge-success rounded-pill counter"><?php echo e($customer->qty_remained); ?></span></small>
                    </div>
                    <small class="text-muted"><?php echo e(number_format($customer->selling_price, 2)); ?> TZS | <?php echo e(strtoupper($customer->stores_tuli->name)); ?> | 
                    <?php if($customer->expire_date == null || $customer->expire_date == 'N/A'): ?>
                        <i>Expiry Status:</i> N/A
                    <?php else: ?>
                        <i>Expiry Status:</i> <?php echo e(\Carbon\Carbon::parse($customer->expire_date)->format('d-M-Y')); ?>

                    <?php endif; ?> 
                </small>
                    </a>
         <?php else: ?>

                    <a wire:click="selectCustomer(<?php echo e($customer); ?>)" class="list-group-item list-group-item-action" href="javascript:void(0)" data-bs-original-title="">
                        <div class="d-flex w-100 justify-content-between">
                            <small><b><?php echo e(strtoupper($customer->product_name)); ?> | <?php echo e(strtoupper($customer->barcode)); ?></b></small>
                        <small class="text-muted">Remained <span class="badge badge-success rounded-pill counter"><?php echo e($customer->qty_remained); ?></span></small>
                    </div>
                    <small class="text-muted"><?php echo e(number_format($customer->selling_price, 2)); ?> TZS | <?php echo e(strtoupper($customer->stores_tuli->name)); ?> | 
                    <?php if($customer->expire_date == null || $customer->expire_date == 'N/A'): ?>
                        <i>Expiry Status:</i> N/A
                    <?php else: ?>
                        <i>Expiry Status:</i> <?php echo e(\Carbon\Carbon::parse($customer->expire_date)->format('d-M-Y')); ?>

                    <?php endif; ?> 
                </small>
                    </a>
                    <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php if(session()->has('error')): ?>
    <div class="alert alert-danger mt-2"><?php echo e(session('error')); ?></div>
<?php endif; ?>
    </div>
  
    </div>
    <!-- ----------------- -->
    <div class="col-lg-8">
      
        <?php if(count($sale)>0): ?>
     
<table class="table table">
   
    <thead>
        <tr>
           
            <th>Product Name</th>
            <th>Barcode</th>
            <th>Qnty</th>
            <th>Price</th>
            <th>Sub Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $total = 0;
        ?>

        <?php $__currentLoopData = $sale; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $subTotal = $item->selling_price * $item->quantity;
                $total += $subTotal;
            ?>
            <tr>
                
                <td><?php echo e($item->products_tuli->product_name); ?></td>
                <td><?php echo e($item->products_tuli->barcode); ?></td> 
                <td><?php echo e($item->quantity); ?></td>
                <td><?php echo e(number_format($item->selling_price, 2)); ?> TZS</td> 
                <td><?php echo e(number_format($subTotal, 2)); ?> TZS</td>
                <td>
                   <!-- <a class='btn btn-outline-info btn-xs'  data-bs-toggle="modal" data-bs-target="#edtQuantity">Add Qnty</a> -->
					<!-- &nbsp;&nbsp;&nbsp; -->
                    <a wire:click="addItem(<?php echo e($item); ?>)" class='btn btn-primary btn-xs btn-outline' href="javascript:void(0)" >+1</a>
                    <a wire:click="removeItem(<?php echo e($item); ?>)" class='btn btn-secondary btn-xs btn-outline' href="javascript:void(0)" >-1</a>
					<!-- &nbsp;&nbsp; -->
					<!-- <a  class="btn btn-info btn-xs" data-bs-toggle="modal" data-bs-target="#addDiscount"><i class='fa fa-tags'></i> Discount</a> -->

                    <a wire:click="deleteItem(<?php echo e($item); ?>)" class='btn btn-danger btn-outline btn-xs pull-right' href="javascript:void(0)" >x</a>
				  </td> 
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <th>Total</th>
            <th></th>
            <th></th>
            <th></th>
            <th><?php echo e(number_format($total, 2)); ?> TZS</th> <!-- Display total -->
        </tr>
    </tbody>
</table>


<div class="f1-buttons">
        <hr />
        <div class="pull-right">

         <?php if(session()->has('message')): ?>
        <div class="alert alert-success">
            <?php echo e(session('message')); ?>

        </div>
    <?php endif; ?>
    <!-- <a href="<?php echo e(Route('invoice-download', ['id' => '2'])); ?>" class="btn btn-outline-primary btn-next" style="margin-top: auto;" type="button">Print <i class="icofont icofont-printer"></i></a> -->

    <button 
    class="btn btn-outline-primary btn-next" 
    wire:click="generateInvoice"
    wire:loading.attr="disabled"
    style="margin-top: auto;"
    type="button"
>
    <span wire:loading.remove>
        Generate / Print Invoice
        <i class="icofont icofont-printer"></i>
    </span>
    <span wire:loading>
        Processing...
        <i class="icofont icofont-spinner-alt-2 icofont-spin"></i>
    </span>
</button>
        <!-- <button 
                class="btn btn-outline-secondary btn-previous "
                wire:click="operateSales" 
                style="margin-top: auto; display: ''" 
                type="button">
           Operate Sale Without Invoice
            <i class="icofont icofont-money"></i> 
        </button> -->

        </div>
</div>

        <?php else: ?>
        Search and select item to add
        <?php endif; ?>

    </div>
    
</div>


   <!-- SEARCH MODAL START -->
   <div class="modal fade" id="edtQuantity" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-info text-white">
                <h5 class="modal-title">Add Quantity</h5>
                
            </div>
            <div class="modal-body">
                <form method="post" action="<?php echo e(url()->current()); ?>">
                    <?php echo csrf_field(); ?>

                    <div class="row">
                        
                        <div class="col-lg-12">
                            <div class="form-group">
                                <input class="form-control" type="text" value="<?php echo e(old('reference_number')); ?>" maxlength="20" required placeholder="Enter quantity">
                            </div>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                
                <button class="btn btn-primary" type="submit" >Add</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- SEARCH MODAL END -->

   <!-- SEARCH MODAL START -->
   <div class="modal fade" id="addDiscount" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-secondary text-white">
                <h5 class="modal-title">Add Discount</h5>
                
            </div>
            <div class="modal-body">
                <form method="post" action="<?php echo e(url()->current()); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <input class="form-control" type="text" value="<?php echo e(old('reference_number')); ?>" maxlength="20" required placeholder="Enter quantity">
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                
                <button class="btn btn-primary" type="submit" >Add</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- SEARCH MODAL END -->

 <div>
    <!-- Livewire Component -->
    <script>
        document.addEventListener('livewire:load', function () {
            Livewire.on('salesUpdated', () => {
                alert('Sales Updated successfully, Do you want to continue with Sales!'); // You can use better notifications like Toastr
            });
        });
    </script>
    <!-- Livewire Component -->
    <!-- <script>
        document.addEventListener('livewire:load', function () {
            Livewire.on('InvoiceUpdated', () => {
                alert('Sales Updated successfully, Do you want to continue with Sales!'); // You can use better notifications like Toastr
            });
        });
    </script> -->
    

</div>

<?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/livewire/tuli-sales-management/oparate-sales.blade.php ENDPATH**/ ?>