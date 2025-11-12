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
      
        <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addTruckRouteModal">New <i class="icofont icofont-plus-circle"></i></button></li>
  

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
                        <?php if(count($TrucksRoute) > 0): ?>
                        <table class="table table-xs table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Trip No</th>
                                    <th>Route Date</th>
                                    <th>Truck Plate No</th>
                                    <th>Driver</th>
                                    <th>Going Customer</th>
                                    <th>Return Customer</th>
                                    <th>Going Fee</th>
                                    <th>Return Fee</th>
                                    <th>Total Fee</th>
                                    <th>Created By</th>
                                    <th>Created Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                    
                            <tbody>
                                <?php $__currentLoopData = $TrucksRoute; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($loop->iteration + ($TrucksRoute->currentPage() - 1) * $TrucksRoute->perPage()); ?></td>
                                        <td><strong><?php echo e($route->trip_no); ?></strong></td>
                                        <td><?php echo e(\Carbon\Carbon::parse($route->route_date)->format('d M Y')); ?></td>
                                        <td><?php echo e($route->our_truck->plate_no ?? 'N/A'); ?></td>
                                        <td><?php echo e($route->our_truck->user->first_name ?? 'Unassigned'); ?></td>
                                        <td><?php echo e($route->going_customer); ?></td>
                                        <td><?php echo e($route->return_customer ?? '-'); ?></td>
                                        <td><?php echo e(number_format($route->going_transport_fee ?? 0, 2)); ?></td>
                                        <td><?php echo e(number_format($route->return_transport_fee ?? 0, 2)); ?></td>
                                        <td>
                                            <?php echo e(number_format(
                                                $route->total_fee ??
                                                (($route->going_transport_fee ?? 0) + ($route->return_transport_fee ?? 0)), 2
                                            )); ?>

                                        </td>
                                        <td><?php echo e($route->user->first_name); ?></td>
                                        <td><?php echo e(\Carbon\Carbon::parse($route->created_date)->format('d M Y h:i A')); ?></td>
                                        <td> <div class="pull-left"> <a href='<?php echo Route('route-preview', ['id' => $route->id]); ?>' class='btn btn-outline-info btn-xs'> view </a></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    
                        
                        <div class="d-flex justify-content-center mt-3">
                            <?php echo e($TrucksRoute->links()); ?>

                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">No truck routes have been added yet.</div>
                    <?php endif; ?>
                    
					</div>
                      </p>
                  </div>
              </div>
          </div>
      </div>
  </div>

 <div class="modal fade" id="addTruckRouteModal" tabindex="-1" role="dialog" aria-labelledby="addTruckRouteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Truck Route</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form method="POST" action="<?php echo e(route('add-truck-route')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Route Date</label>
                                <input type="date" class="form-control" name="route_date" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Trip No</label>
                                <input type="text" class="form-control" id="trip_no" name="trip_no" readonly>
                            </div>
                        </div>
                        

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Truck</label>
                                <select class="form-control" name="truck_id" required>
                                    <option value="">--- Select Truck ---</option>
                                    <?php $__currentLoopData = $OurTruck; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $truck): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($truck->id); ?>"><?php echo e($truck->plate_no); ?> | <?php echo e($truck->user->first_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Going Customer</label>
                                <input type="text" class="form-control" name="going_customer" required placeholder="Enter Going Customer...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Return Customer</label>
                                <input type="text" class="form-control" name="return_customer" placeholder="Enter Return Customer...">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Going Transport Fee</label>
                                <input type="number" step="0.01" class="form-control" name="going_transport_fee" placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Return Transport Fee</label>
                                <input type="number" step="0.01" class="form-control" name="return_transport_fee" placeholder="0.00">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Total Fee</label>
                                <input type="number" step="0.01" class="form-control" name="total_fee" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="created_by" value="<?php echo e(Auth::user()->name); ?>">
                    <input type="hidden" name="created_date" value="<?php echo e(now()); ?>">

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Route</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


 


  <?php $__env->startPush('scripts'); ?>
  <script src="<?php echo e(asset('assets/js/datepicker/date-picker/datepicker.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/js/datepicker/date-picker/datepicker.en.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/js/datepicker/date-picker/datepicker.custom.js')); ?>"></script>
  <script>
   
   
 </script>
 <script>
    document.addEventListener('DOMContentLoaded', function() {
        // When modal opens, fetch trip number
        $('#addTruckRouteModal').on('show.bs.modal', function () {
            fetch('<?php echo e(route("generate-trip-no")); ?>')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('trip_no').value = data.trip_no;
                })
                .catch(err => console.error('Error fetching trip no:', err));
        });
    });
    </script>
    
  <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/admin/sales_management/trip-routes.blade.php ENDPATH**/ ?>