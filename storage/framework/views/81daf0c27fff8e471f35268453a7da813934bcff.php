<?php $__env->startSection('title'); ?>
<?php echo e(ucfirst(str_replace('-',' ',Route::currentRouteName()))); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('css'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/date-picker.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
  <?php $__env->startComponent('components.breadcrumb'); ?>
    

    <?php $__env->slot('breadcrumb_action_buttons'); ?>
    <!-- <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#searchModal">Search <i class="icofont icofont-search-alt-1"></i></button></li> -->
      <?php if(Auth::user()->role == 'ADMIN'): ?>
        <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">New <i class="icofont icofont-plus-circle"></i></button></li>
    <?php endif; ?>

    <?php $__env->endSlot(); ?>
    
    
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
                                    
                                    <th scope="col">Description</th>
                                    <th scope="col">Invetory Date</th>
                                    <th scope="col">Store</th>
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
                                    
                                    <td><a href=""><small><?php echo e($user->description); ?></small></a></td>
                                    <td><a href=""><small><?php echo e($user->inventory_date); ?></small></a></td>
                                    <td><?php echo e($user->stores_tuli->name); ?></td>
                                    <td><?php echo e($user->status); ?></td>
                                    
                                    <td><?php echo e($user->user->first_name); ?></td>
                                    <td>
                                    <div class="pull-left"> <a href='<?php echo Route('inventory-preview-tuli', ['id' => $user->id]); ?>' class='btn btn-outline-info btn-xs'> view </a>
                                    <?php if($user->status == 'Active'): ?>
                                    
                                    <div class="pull-right">
                                        
                                      </div>
    
                                    <?php else: ?>

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
                <h5 class="modal-title">Inventory Registration</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?php echo e(Route('add-inventory-tuli')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-form-label">Supplier / Company Name</label>
                            
                                <!-- Dropdown list -->
                                <select class="form-control" id="companySelect" name="company_name" onchange="handleCompanySelect(this)">
                                    <option value="">-- Select Company --</option>
                                    <?php $__currentLoopData = $companyNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($name); ?>"><?php echo e($name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <option value="__manual__">Other (Add New)</option>
                                </select>
                            
                                <!-- Hidden input for manual entry -->
                                <input class="form-control mt-2 d-none" type="text" id="manualCompanyInput" name="manual_company_name" placeholder="Enter new company name">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">Select Store</label>
                                <select class="form-control"  name="store_id" >
                                    <option value="">-- Select Store --</option>
                                    <?php $__currentLoopData = $companystore; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($store->id); ?>"><?php echo e($store->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-form-label" > Date Received</label>
                                <input class="form-control" type="date" value="<?php echo e(old('branch_name')); ?>" required  name="inventory_date">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Description</label>
                                <textarea class="form-control" type="date" value="<?php echo e(old('branch_name')); ?>" required  name="description">na</textarea>
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
 <script>
    function handleCompanySelect(selectElement) {
        const manualInput = document.getElementById('manualCompanyInput');
    
        if (selectElement.value === '__manual__') {
            manualInput.classList.remove('d-none');
            manualInput.setAttribute('name', 'company_name'); // make it the field that gets submitted
            selectElement.removeAttribute('name'); // avoid duplicate names
        } else {
            manualInput.classList.add('d-none');
            manualInput.removeAttribute('name');
            selectElement.setAttribute('name', 'company_name');
        }
    }
    </script>
    
  <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/admin/tuli_sales_management/inventory-registration.blade.php ENDPATH**/ ?>