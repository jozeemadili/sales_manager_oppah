<?php $__env->startSection('title'); ?>
<?php echo e(ucfirst(str_replace('-', ' ', Route::currentRouteName()))); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<style>
    .info-card {
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 1rem;
        transition: 0.3s;
    }
    .info-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .info-title {
        font-weight: 600;
        color: #0d6efd;
    }
    .info-value {
        font-size: 1rem;
        color: #333;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('breadcrumb_title'); ?>
        <h3><?php echo e(ucfirst(str_replace('-', ' ', Route::currentRouteName()))); ?></h3>
    <?php $__env->endSlot(); ?>

    <?php $__env->slot('breadcrumb_action_buttons'); ?>
        <li>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal_expense">
                Record Expense <i class="icofont icofont-plus-circle"></i>
            </button>
        </li>
        <li>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal_route_plan">
                Route Plan <i class="icofont icofont-plus-circle"></i>
            </button>
        </li>
    <?php $__env->endSlot(); ?>

    <li class="breadcrumb-item"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[0])); ?></li>
    <li class="breadcrumb-item active"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[1])); ?></li>
<?php echo $__env->renderComponent(); ?>

<div class="container-fluid">

    
    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="icon-info-alt txt-danger"></i> <?php echo e($error); ?>

            <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php if($message = Session::get('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="icofont icofont-check-circled"></i> <?php echo $message; ?>

            <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    
    <div class="card shadow-lg border-0 mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="icofont icofont-truck"></i> Truck Route Details & Summary</h5>
            <span class="badge bg-light text-primary">#<?php echo e($TrucksRoute->id); ?></span>
        </div>

        <div class="card-body">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="info-card p-3 bg-light">
                        <p class="info-title mb-1">Trip Number</p>
                        <p class="info-value mb-0"><?php echo e($TrucksRoute->trip_no); ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card p-3 bg-light">
                        <p class="info-title mb-1">Trip Date</p>
                        <p class="info-value mb-0"><?php echo e(\Carbon\Carbon::parse($TrucksRoute->route_date)->format('d M Y')); ?></p>
                    </div>
                </div>
            </div>

            
            <div class="row">
                <div class="col-md-6">
                    <div class="info-card p-3">
                        <p class="info-title mb-1">Going Customer</p>
                        <p class="info-value mb-0"><?php echo e(ucfirst($TrucksRoute->going_customer)); ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card p-3">
                        <p class="info-title mb-1">Return Customer</p>
                        <p class="info-value mb-0"><?php echo e(ucfirst($TrucksRoute->return_customer)); ?></p>
                    </div>
                </div>
            </div>

            
            <?php
                $total_transport_fee = $TrucksRoute->total_fee;
                $total_expenses = $ExpensesRecord->sum('amount_used');
                $total_route_plan = $RoutePlan->sum('amount_tsh');
                $total_costs = $total_expenses + $total_route_plan;
                $balance = $total_transport_fee - $total_costs;
            ?>

            <div class="row">
                <div class="col-md-3">
                    <div class="info-card p-3 text-center bg-light">
                        <h6 class="text-primary mb-1">Total Transport Fee</h6>
                        <h5 class="mb-0 text-success"><?php echo e(number_format($total_transport_fee, 2)); ?> TZS</h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-card p-3 text-center bg-light">
                        <h6 class="text-primary mb-1">Total Route Plan</h6>
                        <h5 class="mb-0 text-info"><?php echo e(number_format($total_route_plan, 2)); ?> TZS</h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-card p-3 text-center bg-light">
                        <h6 class="text-primary mb-1">Total Expenses</h6>
                        <h5 class="mb-0 text-warning"><?php echo e(number_format($total_expenses, 2)); ?> TZS</h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-card p-3 text-center <?php echo e($balance >= 0 ? 'bg-success text-white' : 'bg-danger text-white'); ?>">
                        <h6 class="mb-1">Balance Remaining</h6>
                        <h5 class="mb-0"><?php echo e(number_format($balance, 2)); ?> TZS</h5>
                    </div>
                </div>
            </div>

            
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="info-card p-3">
                        <p class="info-title mb-1">Truck Details</p>
                        <p class="info-value mb-0">Plate No: <?php echo e($TrucksRoute->our_truck->plate_no ?? 'N/A'); ?></p>
                        <p class="info-value mb-0">Driver: <?php echo e($TrucksRoute->our_truck->user->first_name ?? 'Unassigned'); ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card p-3">
                        <p class="info-title mb-1">Created Info</p>
                        <p class="info-value mb-0">By: <?php echo e($TrucksRoute->user->first_name); ?></p>
                        <p class="info-value mb-0">On: <?php echo e(\Carbon\Carbon::parse($TrucksRoute->created_date)->format('d M Y, h:i A')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <ul class="nav nav-tabs card-header-tabs" id="routeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="route-tab" data-bs-toggle="tab" data-bs-target="#routePlans" type="button" role="tab">Route Plans</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="expense-tab" data-bs-toggle="tab" data-bs-target="#expenses" type="button" role="tab">Expenses</button>
                </li>
            </ul>
        </div>

        <div class="card-body tab-content" id="routeTabsContent">
            
            <div class="tab-pane fade show active" id="routePlans" role="tabpanel">
                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newModal_route_plan">
                        + Add Route Plan
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>#</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Distance (KM)</th>
                                <th>Fuel (Litres)</th>
                                <th>Amount (TZS)</th>
                                <th>Description</th>
                                <th>Created By</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $RoutePlan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($index + 1); ?></td>
                                    <td><?php echo e(ucfirst($plan->from_location)); ?></td>
                                    <td><?php echo e(ucfirst($plan->to_location)); ?></td>
                                    <td><?php echo e(number_format($plan->distance_km, 0)); ?></td>
                                    <td><?php echo e(number_format($plan->fuel_litres, 0)); ?></td>
                                    <td><?php echo e(number_format($plan->amount_tsh, 2)); ?></td>
                                    <td><?php echo e($plan->description); ?></td>
                                    <td><?php echo e($plan->user->first_name ?? 'N/A'); ?></td>
                                    <td><?php echo e(\Carbon\Carbon::parse($plan->created_at)->format('d M Y H:i')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted">No route plans found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            
            <div class="tab-pane fade" id="expenses" role="tabpanel">
                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newModal_expense">
                        + Record Expense
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>#</th>
                                <th>Expense Name</th>
                                <th>Amount Used</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Created By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $ExpensesRecord; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($loop->index + 1); ?></td>
                                    <td><?php echo e(strtoupper($b->expense->e_name)); ?></td>
                                    <td><?php echo e(number_format($b->amount_used, 2)); ?></td>
                                    <td><?php echo e(strtoupper($b->desr)); ?></td>
                                    <td><?php echo e($b->status); ?></td>
                                    <td><?php echo e($b->user->first_name); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No expenses found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>




<?php $__env->stopSection(); ?>

    <!-- NEW MODAL START -->
    <div class="modal fade" id="newModal_expense" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Expense Registration</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="<?php echo e(Route('record-expense-truck')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-form-label">Expenses</label>
                                <select class="form-control" required name="expense_id">
                                <option value="">--- Choose Expense ---</option>  
                                            <?php $__currentLoopData = $Expense; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value='<?php echo e($st->id); ?>'><?php echo e(strtoupper($st->e_name)); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
    
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-form-label">Amount Used</label>
                                <input class="form-control" type="number" required name="amount_used" >
                            </div>
                        </div>
                       
                        
                    </div>
    
                    <div class="row">
                    <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-form-label">Details</label>
                                <textarea class="form-control" name="desr" rows="3" placeholder="Briefly describe ...">na</textarea>
                            </div>
                            
                        </div>
                       <input type="text" hidden value="<?php echo e($TrucksRoute->id); ?>" name="inventory_id">
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
    
    
    
       <!-- NEW MODAL START -->
    <div class="modal fade" id="newModal_route_plan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Route Plan</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
    
                <div class="modal-body">
                    <form method="post" action="<?php echo e(Route('record-route-plan')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <!-- FROM -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="col-form-label">From</label>
                                    <input class="form-control" type="text" name="from_location" required placeholder="Enter starting point">
                                </div>
                            </div>
    
                            <!-- TO -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="col-form-label">To</label>
                                    <input class="form-control" type="text" name="to_location" required placeholder="Enter destination">
                                </div>
                            </div>
                        </div>
    
                        <div class="row mt-3">
                            <!-- Distance -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="col-form-label">Distance (KM)</label>
                                    <input class="form-control" type="number" name="distance_km" required placeholder="e.g. 350">
                                </div>
                            </div>
    
                            <!-- Fuel -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="col-form-label">Fuel (Litres)</label>
                                    <input class="form-control" type="number" name="fuel_litres" step="0.01" required placeholder="e.g. 120">
                                </div>
                            </div>
    
                            <!-- Amount -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="col-form-label">Amount (Tsh)</label>
                                    <input class="form-control" type="number" name="amount_tsh" required placeholder="e.g. 250000">
                                </div>
                            </div>
                        </div>
    
                        <div class="row mt-3">
                            <!-- Description -->
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="col-form-label">Remarks / Details</label>
                                    <textarea class="form-control" name="description" rows="3" placeholder="Briefly describe route purpose..."></textarea>
                                </div>
                            </div>
    
                            <!-- Hidden Truck Route ID -->
                            <input type="hidden" value="<?php echo e($TrucksRoute->id); ?>" name="inventory_id">
                        </div>
    
                        <div class="modal-footer mt-4">
                            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                            <button class="btn btn-primary" type="submit">Confirm & Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/admin/sales_management/routes-preview.blade.php ENDPATH**/ ?>