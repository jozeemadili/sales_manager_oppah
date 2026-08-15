<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startPush('css'); ?>

<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container">

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
                                <?php if(Auth::user()->role == 'ADMIN'): ?>
                                <div class="form-group">
                                    <label>Truck</label>
                                    <select class="form-control" name="truck_id" required>
                                        <option value="">--- Select Truck ---</option>
                                        <?php $__currentLoopData = $OurTruck; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $truck): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($truck->id); ?>">
                                                <?php echo e($truck->plate_no); ?> | <?php echo e($truck->driver?->first_name ?? 'Unassigned'); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                    
                            <?php elseif(Auth::user()->role == 'Driver'): ?>
                                <?php
                                    // Since controller limits trucks by driver, we just grab the first (or only) one
                                    $truck = $OurTruck->first();
                                ?>
                    
                                <div class="form-group">
                                    <label>My Truck</label>
                                    <?php if($truck): ?>
                                        
                                        <input type="hidden" name="truck_id" value="<?php echo e($truck->id); ?>">
                    
                                        
                                        <input type="text" class="form-control" 
                                               value="Plate no : <?php echo e($truck->plate_no); ?>, Driver : <?php echo e($truck->driver?->first_name ?? 'Unassigned'); ?>" 
                                               readonly>
                                    <?php else: ?>
                                        <input type="text" class="form-control" value="No truck assigned" readonly>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
    
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Going Transport Fee</label>
                                    <input type="number" step="0.01" class="form-control" name="going_transport_fee" placeholder="0.00">
                                </div>
                            </div>
    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Return Transport Fee</label>
                                    <input type="number" step="0.01" class="form-control" name="return_transport_fee" placeholder="0.00">
                                </div>
                            </div>
    
                            
                        </div>
    
                        <input type="hidden" name="created_by" value="<?php echo e(Auth::user()->name); ?>">
                        <input type="hidden" name="created_date" value="<?php echo e(now()); ?>">
    
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary w-100">Save Route</button>

                        </div>
                    </form>
                </div>
          
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
  <script src="<?php echo e(asset('assets/js/datepicker/date-picker/datepicker.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/js/datepicker/date-picker/datepicker.en.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/js/datepicker/date-picker/datepicker.custom.js')); ?>"></script>
  <script>
   
   
 </script>
 <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch trip number immediately when page loads
        fetch('<?php echo e(route("generate-trip-no")); ?>')
            .then(response => response.json())
            .then(data => {
                // Set trip number input value
                const tripInput = document.getElementById('trip_no');
                if (tripInput) {
                    tripInput.value = data.trip_no;
                }
            })
            .catch(err => console.error('Error fetching trip no:', err));
    });
    </script>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/admin/dashboard/driver-home.blade.php ENDPATH**/ ?>