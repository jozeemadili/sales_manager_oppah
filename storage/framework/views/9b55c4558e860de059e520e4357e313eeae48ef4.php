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
    <!-- <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#searchModal">Search <i class="icofont icofont-search-alt-1"></i></button></li> -->
      <?php if(Auth::user()->role == 'ADMIN'): ?>
        
    <?php endif; ?>

    <?php $__env->endSlot(); ?>
    
    <li class="breadcrumb-item"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[0])); ?></li>
    <li class="breadcrumb-item active"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[1])); ?></li>
  <?php echo $__env->renderComponent(); ?>
  
  <div class="container-fluid">
      <div class="row">
          <div class="col-sm-12">
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
									<th scope="col">Supplier Name</th>
                                    <th scope="col">Vihecle No</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Invetory Date</th>
                                
                                    <th scope="col">Status</th>
                                    <th scope="col">Created By</th>
                                    <th scope="col">Action</th>
								</tr>
							</thead>

							<tbody>
                                <?php $__currentLoopData = $Branch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<th scope="row"><?php echo e($loop->index + 1); ?>.</th>
									<td><a href=""><small><?php echo e(strtoupper($user->company_name)); ?></small></a></td>
                                    <td><a href=""><small><?php echo e(strtoupper($user->vihecle_no)); ?></small></a></td>
                                    <td><a href=""><small><?php echo e($user->description); ?></small></a></td>
                                    <td><a href=""><small><?php echo e($user->inventory_date); ?></small></a></td>
                                    <td><?php echo e($user->status); ?></td>
                                    <td><?php echo e($user->user->first_name); ?></td>
                                    <td>
                                    <div class="pull-right"> <a href='<?php echo Route('inventory-preview', ['id' => $user->id]); ?>' class='btn btn-outline-info btn-xs'> view </a>
                                    
                                    
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



  <?php $__env->startPush('scripts'); ?>
  <script src="<?php echo e(asset('assets/js/datepicker/date-picker/datepicker.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/js/datepicker/date-picker/datepicker.en.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/js/datepicker/date-picker/datepicker.custom.js')); ?>"></script>
  <script>
    document.getElementById("reference_number").placeholder = "Enter user's ID Number ...";
    document.getElementById('reference_number').name = 'id_number';
    function searchBy(search_by)
    {
        if(search_by.value == "id_number")
        {
            document.getElementById("reference_number").type = "text";
            document.getElementById("reference_number").placeholder = "Enter user's ID Number ...";
            document.getElementById('reference_number').name = 'id_number';
        }
        else if(search_by.value == "phone_number")
        {
            document.getElementById("reference_number").maxlength = "9";
            document.getElementById("reference_number").type = "number";
            document.getElementById("reference_number").placeholder = "Enter user's Phone (e.g. 766192332) ...";
            document.getElementById('reference_number').name = 'phone_number';
            
        }
        else
        {
            document.getElementById("reference_number").type = "text";
            document.getElementById("reference_number").placeholder = "Enter user's First Name or Middle Name or Last Name ...";
            document.getElementById('reference_number').name = 'cname';
        }
    }
 </script>
 
  <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/admin/sales_management/suppliers-list-items.blade.php ENDPATH**/ ?>