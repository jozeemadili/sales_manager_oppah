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
									<th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Mobile</th>
                                    <th scope="col">Created By</th>
                                    <th scope="col">Action</th>
								</tr>
							</thead>
							<tbody>
                                <?php $__currentLoopData = $Branch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
                                <!-- SELECT `id`, ``, `tin`, `vrn`, ``, ``, `physical_addres`, `status`, `created_at`, `created_by`, `company_id` FROM `customers` WHERE 1 -->
									<th scope="row"><?php echo e($loop->index + 1); ?>.</th>

                                    <td><a href="<?php echo e(Route('customer-profile', ['id' => $user->id])); ?>"><small><?php echo e(strtoupper($user->name)); ?></small></a></td>
                                    <td><a href=""><small><?php echo e($user->email); ?></small></a></td>
                                    <td><?php echo e($user->phone); ?></td>
                                    <!-- <td><?php echo e($user->user->first_name); ?></td> -->
                                    <td>
                                    <?php if($user->status == 'Active'): ?>
                                    
                                    <?php else: ?>
                                    <!-- <div class="pull-left"> <a href='<?php echo Route('branch-status-update', ['id' => $user->id, 'status' => 'Active']); ?>' class='btn btn-outline-info btn-xs'>Activate <i class="icofont icofont-ui-delete"></i></a></div>   -->
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
                <h5 class="modal-title">Category Registration</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?php echo e(Route('add-customer')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label class="col-form-label">Name</label>
            <input class="form-control" type="text" value="<?php echo e(old('name')); ?>" required name="name" placeholder="Write Customer's full Name/Company Name...">
        </div>
        
        <div class="form-group">
            <label class="col-form-label">Telephone</label>
            <input class="form-control" type="text" value="<?php echo e(old('phone')); ?>" required name="phone" placeholder="Write Customer's Telephone...">
        </div>
        <div class="form-group">
            <label class="col-form-label">Email</label>
            <input class="form-control" type="text" value="<?php echo e(old('email')); ?>" required name="email" placeholder="Write Customer's Email...">
        </div>
    </div>
    <div class="col-lg-6">
       
        <div class="form-group">
            <label class="col-form-label">TIN</label>
            <input class="form-control" type="text" value="<?php echo e(old('tin')); ?>" required name="tin" placeholder="Write Customer's TIN...">
        </div>
        <div class="form-group">
            <label class="col-form-label">VRN</label>
            <input class="form-control" type="text" value="<?php echo e(old('vrn')); ?>" required name="vrn" placeholder="Write Customer's VRN...">
        </div>
        <div class="form-group">
            <label class="col-form-label">Address</label>
            <input class="form-control" type="text" value="<?php echo e(old('physical_addres')); ?>" required name="physical_addres" placeholder="Write Customer's Physical Address, COUNTRY/CITY/STREET...">
        </div>
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
                                <label class="col-form-label">Search By</label><br>
                                <input type="radio" checked class="radio_animated" value="id_number" name="search_by" id="search_by" onChange="searchBy(this)"> email.
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" class="radio_animated" value="phone_number" name="search_by" id="search_by" onChange="searchBy(this)"> Phone No.
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" class="radio_animated" value="name" name="search_by" id="search_by" onChange="searchBy(this)"> Name
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <input class="form-control" type="text" value="<?php echo e(old('reference_number')); ?>" maxlength="20" required id="reference_number">
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
    document.getElementById("reference_number").placeholder = "Enter Email ...";
    document.getElementById('reference_number').name = 'id_number';
    function searchBy(search_by)
    {
        if(search_by.value == "id_number")
        {
            document.getElementById("reference_number").type = "text";
            document.getElementById("reference_number").placeholder = "Enter Email ...";
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

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/admin/sales_management/customers-registration.blade.php ENDPATH**/ ?>