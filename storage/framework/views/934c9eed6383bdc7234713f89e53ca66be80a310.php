<?php $__env->startSection('title'); ?>
<?php echo e(ucfirst(str_replace('-',' ',Route::currentRouteName()))); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('css'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/date-picker.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
  <?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('breadcrumb_title'); ?>
      <h3><?php echo e(ucfirst(str_replace('-',' ',Route::currentRouteName()))); ?></h3>
    <?php $__env->endSlot(); ?>

    <?php $__env->slot('breadcrumb_action_buttons'); ?>
    <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#searchModal">Search <i class="icofont icofont-search-alt-1"></i></button></li>
      
        <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">New <i class="icofont icofont-plus-circle"></i></button></li>
  

    <?php $__env->endSlot(); ?>
    
    <li class="breadcrumb-item"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[0])); ?></li>
    <li class="breadcrumb-item active"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[1])); ?></li>
  <?php echo $__env->renderComponent(); ?>
  
  <div class="container-fluid">
      <div class="row">
          <div class="col-sm-12">
          <?php if($errors->any()): ?>
    <div class="alert alert-danger">
        <ul>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

              <div class="card">

                  <div class="card-body">
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

                    <p>
                      <div class="table-responsive">
                        <?php if(count($Branch)>0): ?>
						<table class="table table-xs">
							<thead>
								<tr>
									<th scope="col">#</th>
									<th scope="col">product Name</th>
                                    <th scope="col">category</th>
                                    <th scope="col">store</th>
                                    <th scope="col">barcode</th>
                                    <th scope="col">quantity Recorded</th>
                                    <th scope="col">quantity Remained</th>
                                    <?php if(Auth::user()->role == 'ADMIN'): ?>
                                    <th scope="col">purchasing price</th>
                                    <?php endif; ?>
                                    <th scope="col">selling price</th>
                                    <?php if(Auth::user()->role == 'ADMIN'): ?>
                                    <th scope="col"> Expected profit/loss</th>
                                    <?php endif; ?>
                                    
                                    <th scope="col">Status</th>
                                    <th scope="col">Created By</th>
                                    <th scope="col">Action</th>
								</tr>
							</thead>
							<tbody>
                                <?php $__currentLoopData = $Branch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tr>
									<th scope="row"><?php echo e($loop->index + 1); ?>.</th>
									<td><a href=""><small><?php echo e(strtoupper($user->product_name)); ?></small></a></td>
                                    <td><a href=""><small><?php echo e(strtoupper($user->categories_tuli->name)); ?></small></a></td>
                                    <td><a href=""><small><?php echo e(strtoupper($user->stores_tuli->name)); ?></small></a></td>
                                    <td><?php echo e($user->barcode); ?></td>
                                    <td><?php echo e(number_format($user->qty, 2)); ?></td>
                                    <td><?php echo e(number_format($user->qty_remained, 2)); ?></td>
                                    <?php if(Auth::user()->role == 'ADMIN'): ?>
                                    <td><?php echo e(number_format($user->purchasing_price, 2)); ?></td>
                                    <?php endif; ?>
                                    <td><?php echo e(number_format($user->selling_price, 2)); ?></td>
                                    <?php if(Auth::user()->role == 'ADMIN'): ?>
                                    <td style="color: <?php echo e(($user->qty * $user->selling_price) - ($user->qty * $user->purchasing_price)   < 0 ? 'red' : 'green'); ?>;">
                                        <?php echo e(number_format(($user->qty * $user->selling_price) - ($user->qty * $user->purchasing_price), 2)); ?>

                                    </td>
                                    <?php endif; ?>

                                    <td><?php echo e($user->status); ?></td>
                                    
                                    <td><?php echo e($user->user->first_name); ?></td>
                                    <td>
                                    <?php if($user->status == 'Active'): ?>
                                    <!-- <div class="pull-left"> <a href='<?php echo Route('branch-status-update', ['id' => $user->id, 'status' => 'Inactive']); ?>' class='btn btn-outline-danger btn-xs'>Deactivate <i class="icofont icofont-ui-delete"></i></a> -->
                                    <a class='btn btn-outline-info btn-xs'  data-bs-toggle="modal"  data-bs-target="#editqtyModal<?php echo e($user->id); ?>">Edit Product</a>
                                    <!-- <a class='btn btn-outline-info btn-xs'  data-bs-toggle="modal"  data-bs-target="#transferStore<?php echo e($user->id); ?>">Change Store</a> -->
                                </div> 

                                    <?php else: ?>
                                    <div class="pull-left"> <a href='<?php echo Route('branch-status-update', ['id' => $user->id, 'status' => 'Active']); ?>' class='btn btn-outline-info btn-xs'>Activate <i class="icofont icofont-ui-delete"></i></a></div>  
                                    <?php endif; ?>
                                    
							</td>
                                   	</tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
                        <br />
                        <?php echo e($Branch->links()); ?>


                        <?php else: ?> 
                        <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                            <i class="icon-info-alt txt-danger"></i>
								No Records Found yet
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" ></button>
                       	</div>
                        <?php endif; ?>
					</div>
                      </p>
                  </div>
              </div>
          </div>
      </div>
  </div>

 <!-- NEW MODAL START -->
 <div class="modal fade" id="newModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Produc Registration</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?php echo e(Route('add-product')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Stock/Product</label>
                            <input class="form-control" type="text" required name="product_name" placeholder="Commodity Name...">
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Qnty</label>
                            <input class="form-control" type="number" required name="qty" value="1">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Unit Of Measure</label>
                            <input class="form-control" type="text" required name="unit_of_measuer" value="">
                        </div>
                    </div>
                   
                    
                </div>

                <div class="row">
                    

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Purchasing Price</label>
                            <input class="form-control" type="text" required name="purchasing_price" placeholder="Purchasing Price...">
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Selling Price</label>
                            <input class="form-control" type="text" required name="selling_price" placeholder="Selling Price...">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Store</label>
                            <select class="form-control" required name="store_id">
                            <option value="">--- Choose Store ---</option>  
                                        <?php $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value='<?php echo e($st->id); ?>'><?php echo e(strtoupper($st->name)); ?> - <?php echo e(strtoupper($st->physica_addres)); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Details</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Briefly describe the product..."></textarea>
                        </div>
                        
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Barcode</label>
                            <input class="form-control" type="text" required name="barcode" value="<?php echo e($barcodeValue); ?>">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        
                    </div>
                   
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm & Register</button>
                </form>
            </div>
        </div>
    </div>
    </div>

   <!-- SEARCH MODAL START -->
   <div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-primary text-white">
                <h5 class="modal-title">User Search</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?php echo e(url()->current()); ?>">
                    <?php echo csrf_field(); ?>

                    <div class="row">

                        <div class="col-lg-12">
                            <div class="form-group">
                            <label class="col-form-label">Search By Product Name</label><br>
                                <input class="form-control" type="text" value="<?php echo e(old('cname')); ?>" name ="cname" required >
                            </div>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Search</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- SEARCH MODAL END -->

  <?php $__env->startPush('scripts'); ?>
  <script src="<?php echo e(asset('assets/js/datepicker/date-picker/datepicker.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/js/datepicker/date-picker/datepicker.en.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/js/datepicker/date-picker/datepicker.custom.js')); ?>"></script>
  <script>
   
   
 </script>
  <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php $__currentLoopData = $Branch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="editqtyModal<?php echo e($user->id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Edit Details For Item : <?php echo e(strtoupper($user->product_name)); ?></h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                <form method="post" action="<?php echo e(Route('edit-item-product-details')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="product_id"value="<?php echo e($user->id); ?>">
                    <div class="row">
                    <div class="col-lg-4">

                        <div class="form-group">
                            <label class="col-form-label">Stock/Product</label>
                            <input class="form-control" type="text" required name="product_name" placeholder="Commodity Name..." value="<?php echo e($user->product_name); ?>">
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Quantity Recorded</label> 
                            <input class="form-control" type="number" required name="qty" value="<?php echo e($user->qty); ?>" >
                            <i>Quantity Remained : <b><?php echo e($user->qty_remained); ?> <?php echo e($user->unit_of_measuer); ?></b></i>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Unit Of Measure</label>
                            <input class="form-control" type="text" required name="unit_of_measuer" value="<?php echo e($user->unit_of_measuer); ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Purchasing Price</label>
                            <input class="form-control" type="text" required name="purchasing_price" placeholder="Purchasing Price..." value="<?php echo e($user->purchasing_price); ?>">
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Selling Price</label>
                            <input class="form-control" type="text" required name="selling_price" placeholder="Selling Price..." value="<?php echo e($user->selling_price); ?>">
                        </div>
                    </div>
                    
                    
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Reason For Edit</label>
                            <textarea class="form-control" name="reason_for_editing" rows="3" placeholder="Briefly describe the product..." required></textarea>
                        </div>
                        
                        
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Barcode</label>
                            <input class="form-control" type="text" required readonly name="barcode" value="<?php echo e($user->barcode); ?>">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="col-form-label">Category </label> 
                            <select class="form-control" required name="category">
                           
                            <option value="">--- Choose Category ---</option>  
                                        <?php $__currentLoopData = $Categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value='<?php echo e($cat->id); ?>'><?php echo e(strtoupper($cat->name)); ?> </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <i>Previous was Category in This : <b><?php echo e($user->categories_tuli->name); ?> Category</b></i>
                        </div>
                    </div>
                   
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

<?php $__currentLoopData = $Branch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="transferStore<?php echo e($user->id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"> Transfer Item : <?php echo e(strtoupper($user->product_name)); ?> To new Store</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                <form method="post" action="<?php echo e(Route('transfer-item-product-details')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="product_id"value="<?php echo e($user->id); ?>">
                    

                <div class="row">
                    
                <div class="col-lg-6">
                        <div class="form-group">
                            <label class="col-form-label"> Choose New Store</label>
                            <select class="form-control" required name="new_store_id">
                            <option value="">--- Choose Store ---</option>  
                                        <?php $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value='<?php echo e($st->id); ?>'><?php echo e(strtoupper($st->name)); ?> - <?php echo e(strtoupper($st->physica_addres)); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <i>Previous was  in This Store : <b><?php echo e($user->stores_tuli->name); ?> </b></i>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Quantity To Transfer</label> 
                            <input class="form-control" type="number" required name="qty" min="1" max="<?php echo e($user->qty_remained); ?>" >
                            <i>Quantity Remained : <b><?php echo e($user->qty_remained); ?> <?php echo e($user->unit_of_measuer); ?></b></i>
                        </div>
                    </div>

                    

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="col-form-label">Reason For Transfer</label>
                            <textarea class="form-control" name="reason_for_transfer" rows="3" placeholder="Briefly describe the product..." required></textarea>
                        </div>
                    </div>
                   
                </div>
                    <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm &  Transfer</button>
                </form>
            </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/admin/tuli_sales_management/products-registration.blade.php ENDPATH**/ ?>