<?php $__env->startSection('title'); ?>
Truck Routes Report
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/date-picker.css')); ?>">

<style>
    .summary-card {
        border-radius: 10px;
        padding: 18px;
        box-shadow: 0px 2px 8px rgba(0,0,0,0.05);
        transition: 0.3s ease-in-out;
    }

    .summary-card:hover {
        transform: translateY(-3px);
    }

    .summary-title {
        font-size: 14px;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .summary-value {
        font-size: 22px;
        font-weight: 700;
    }

    .balance-positive { color: #28a745 !important; }
    .balance-negative { color: #dc3545 !important; }

    .report-table td, .report-table th {
        vertical-align: middle;
        font-size: 13px;
    }

    .truck-info small {
        color: #6c757d;
        font-size: 11px;
    }

    .filter-badge {
        background: #e3f2fd;
        border-left: 4px solid #0d6efd;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 15px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('breadcrumb_title'); ?>
        <h3><i class="icofont icofont-chart-histogram"></i> Truck Routes Report</h3>
    <?php $__env->endSlot(); ?>

    <?php $__env->slot('breadcrumb_action_buttons'); ?>
        <li>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="icofont icofont-search-alt-1"></i> Filter
            </button>
        </li>
    <?php $__env->endSlot(); ?>

    <li class="breadcrumb-item">Reports</li>
    <li class="breadcrumb-item active">Truck Routes</li>
<?php echo $__env->renderComponent(); ?>


<div class="container-fluid">
    <div class="row">

        <div class="col-sm-12">

            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Summary Overview</h5>
                    <?php if($selectedTruck || $selectedMonth): ?>
                        <span class="badge bg-info">Filtered</span>
                    <?php endif; ?>
                </div>

                <div class="card-body">

                    
                    <div class="filter-badge">
                        <strong><i class="icofont icofont-filter"></i> Applied Filters:</strong><br>

                        
                        <strong>Truck:</strong>
                        <?php if($selectedTruck): ?>
                            <?php
                                $truck = $trucks->where('id', $selectedTruck)->first();
                            ?>
                            <?php echo e($truck->plate_no ?? 'Unknown'); ?> 
                            — Driver: <?php echo e($truck->driver->first_name ?? 'N/A'); ?>

                        <?php else: ?>
                            <em>All Trucks</em>
                        <?php endif; ?>
                        <br>

                        
                        <strong>Month:</strong>
                        <?php if($selectedMonth): ?>
                            <?php echo e(\Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y')); ?>

                        <?php else: ?>
                            <em>Any Month</em>
                        <?php endif; ?>
                    </div>

                    
                    <div class="row g-3">

                        <div class="col-md-3">
                            <div class="summary-card bg-light">
                                <div class="summary-title">Total Transport Fee</div>
                                <div class="summary-value text-success"><?php echo e(number_format($totalTransportFee, 2)); ?> TZS</div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="summary-card bg-light">
                                <div class="summary-title">Total Route Fuel</div>
                                <div class="summary-value text-warning"><?php echo e(number_format($totalRouteFuel, 2)); ?> TZS</div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="summary-card bg-light">
                                <div class="summary-title">Total Expenses</div>
                                <div class="summary-value text-danger"><?php echo e(number_format($totalExpenses, 2)); ?> TZS</div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="summary-card bg-light">
                                <div class="summary-title">Balance Remaining</div>
                                <div class="summary-value 
                                    <?php echo e($balanceRemaining >= 0 ? 'balance-positive' : 'balance-negative'); ?>">
                                    <?php echo e(number_format($balanceRemaining, 2)); ?> TZS
                                </div>
                            </div>
                        </div>

                    </div>

                    <hr>

                    
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-striped report-table">
                            <thead class="table">
                                <tr>
                                    <th>Trip</th>
                                    <th>Date</th>
                                    <th>Truck</th>
                                    <th>Going</th>
                                    <th>Return</th>
                                    <th>Fee</th>
                                    <th>Fuel</th>
                                    <th>Expenses</th>
                                    <th>Balance</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php $__currentLoopData = $TrucksRoutes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $routeFuel = $route->routePlans->sum('amount_tsh');
                                    $routeExpenses = $route->expensesRecords->sum('amount_used');
                                    $routeBalance = $route->total_fee - $routeFuel - $routeExpenses;
                                ?>

                                <tr>
                                    <td><strong><?php echo e($route->trip_no); ?></strong></td>
                                    <td><?php echo e(\Carbon\Carbon::parse($route->route_date)->format('d M Y')); ?></td>

                                    <td class="truck-info">
                                        <strong><?php echo e($route->our_truck->plate_no ?? 'N/A'); ?></strong><br>
                                        <small><?php echo e($route->our_truck->driver->first_name ?? 'N/A'); ?></small>
                                    </td>

                                    <td><?php echo e($route->going_customer); ?></td>
                                    <td><?php echo e($route->return_customer); ?></td>

                                    <td class="text-success"><?php echo e(number_format($route->total_fee, 2)); ?></td>
                                    <td class="text-warning"><?php echo e(number_format($routeFuel, 2)); ?></td>
                                    <td class="text-danger"><?php echo e(number_format($routeExpenses, 2)); ?></td>

                                    <td class="<?php echo e($routeBalance >= 0 ? 'text-success' : 'text-danger'); ?>">
                                        <strong><?php echo e(number_format($routeBalance, 2)); ?></strong>
                                    </td>
                                    <td>
                                        <div class="pull-left"> <a href='<?php echo Route('route-preview', ['id' => $route->id]); ?>' class='btn btn-outline-info btn-xs'> view </a>
                                            
                                    </td>
                                </tr>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                </div> 
            </div> 
        </div>
    </div>
</div>



<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <form method="GET" action="<?php echo e(route('truck-reports')); ?>" class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="icofont icofont-filter"></i> Filter Report
                </h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Select Truck</label>
                    <select name="truck_id" class="form-control">
                        <option value="">-- Any Truck --</option>
                        <?php $__currentLoopData = $trucks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $truck): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($truck->id); ?>" <?php echo e($truck->id == $selectedTruck ? 'selected' : ''); ?>>
                                <?php echo e($truck->plate_no); ?> — <?php echo e($truck->driver->first_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Select Month</label>
                    <input type="month" name="month" class="form-control" value="<?php echo e($selectedMonth); ?>">
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" type="submit">
                    <i class="icofont icofont-search"></i> Apply Filters
                </button>
            </div>

        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/admin/sales_management/truck-reports.blade.php ENDPATH**/ ?>