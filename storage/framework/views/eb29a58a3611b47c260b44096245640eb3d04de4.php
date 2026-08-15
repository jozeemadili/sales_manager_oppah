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
      <?php if(Auth::user()->role == 'ADMIN'): ?>
        <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">New <i class="icofont icofont-plus-circle"></i></button></li>
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
                        <?php if(count($users)>0): ?>
						<table class="table table-xs">
							<thead>
								<tr>
									<th scope="col">#</th>
									<th scope="col">Name</th>
                                      <th scope="col">Mobile</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Ofice location</th>
                                    <th scope="col">Action</th>
								</tr>
							</thead>
							<tbody>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr>
									<th scope="row"><?php echo e($loop->index + 1); ?>.</th>
									<td><a href=""><small><?php echo e(strtoupper($user->first_name)); ?></small></a></td>
									        <td>+255<?php echo e($user->mobile); ?></td>
                                    <td><?php echo e($user->email); ?></td>
                                    <td><?php echo e($user->role); ?></td>
                                    <td><?php echo e($user->status); ?></td>
                                    <td><?php echo e($user->office_location); ?> | 
                                        <?php echo e(\App\Http\Controllers\Stock\ProductsController::storeName($user->office_location)); ?>


                                    </td>
                                    <td>
                                    <a  class="btn btn-primary btn-xs" data-bs-toggle="modal" data-bs-target="#approvalModal<?php echo e($user->id); ?>"><i class='fa fa-tags'></i> Store</a>
                                    <?php if($user->status == 'Active'): ?>
                                    <div class="pull-left"> <a href='<?php echo Route('user-status-update', ['id' => $user->id, 'status' => 'Inactive']); ?>' class='btn btn-outline-danger btn-xs'>Deactivate <i class="icofont icofont-ui-delete"></i></a></div>  
                                   
                                    <?php else: ?>
                                    <div class="pull-left"> <a href='<?php echo Route('user-status-update', ['id' => $user->id, 'status' => 'Active']); ?>' class='btn btn-outline-info btn-xs'>Activate <i class="icofont icofont-ui-delete"></i></a></div>  
                                    <?php endif; ?>
                                    
							</td>
                                   	</tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
                        <br />
                        <?php echo e($users->links()); ?>


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
                <h5 class="modal-title">User Registration</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?php echo e(Route('portal-users-add')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="col-form-label" >Full Name</label>
                                <input class="form-control" type="text" value="<?php echo e(old('first_name')); ?>" required  name="first_name">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Username</label>
                                <input class="form-control" type="text" value="<?php echo e(old('first_name')); ?>" required  name="username">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Role</label>
                                <select class="form-select" required value="<?php echo e(old('role')); ?>" name="role">
                                    <option value="">--- Choose User Role ---</option>  
                                    <?php if(Auth::user()->role == 'ADMIN'): ?>  
                                        <!-- <option>ADMIN</option> -->
                                        <option>Supervisor</option>
                                        <option>Driver</option>
                                        <option>Operator</option>
                                     <?php endif; ?>
								</select>
                            </div>
                        </div>


                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="col-form-label" >Email Address</label>
                                <input class="form-control" type="text" value="<?php echo e(old('email')); ?>" required  name="email">
                            </div>
                            
                            <div class="form-group">
                                <label class="col-form-label" >Choose Password</b></label>
                                <input class="form-control" type="password"  minlength="8"  value="<?php echo e(old('password')); ?>" required  name="password">
                            </div>

                          
                            
                        </div>


                        <div class="col-lg-4">
                            
                        <div class="form-group">
                                <label class="col-form-label" >Phone Number</label>
                                <input class="form-control" type="text" minlength="9" maxlength="9" value="<?php echo e(old('mobile')); ?>" required name="mobile" >
                        </div>

                        <div class="form-group">
                                <label class="col-form-label" >Retype Password </label>
                                <input class="form-control" type="password"  minlength="8"  value="<?php echo e(old('password')); ?>" required  name="password">
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
 <!-- NEW MODAL END -->

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
                                <input type="radio" checked class="radio_animated" value="id_number" name="search_by" id="search_by" onChange="searchBy(this)"> Identity No.
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
<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="approvalModal<?php echo e($user->id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-body">
                <div class="table-responsive">
                <form method="post" action="<?php echo e(Route('edit-user-store')); ?>">
                    <?php echo csrf_field(); ?>
                <div class="form-group">
               
                            <label class="col-form-label" >Choose Store</label>
                                <select class="form-select" required name="store_id">
                                <option value="">--- Choose Store ---</option>  
                                        <?php $__currentLoopData = $Store; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value='<?php echo e(strtoupper($s->id)); ?>'><?php echo e(strtoupper($s->name)); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
                                
                                <input type="hidden" name="user_id"value="<?php echo e($user->id); ?>">
                                
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
<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/admin/security/users.blade.php ENDPATH**/ ?>