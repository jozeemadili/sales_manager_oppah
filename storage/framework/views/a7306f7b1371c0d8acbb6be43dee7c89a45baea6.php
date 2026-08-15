<?php $__env->startSection('title'); ?>
    Sales Report
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div class="container-fluid">

    
    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="icon-info-alt txt-danger"></i>
            <?php echo e($error); ?>

            <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="icofont icofont-check-circled"></i>
            <?php echo session('success'); ?>

            <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- ========================= FILTER ========================= -->

    <div class="card mb-3">

        <div class="card-header">
            <h5 class="mb-0">
                <i class="fa fa-filter"></i>
                Sales Report Filters
            </h5>
        </div>

        <div class="card-body">

            <form id="filterForm" method="GET" action="<?php echo e(route('sales-report-tuli')); ?>">

                <div class="row">

                    
                    <div class="col-md-3 mb-3">
                        <label><strong>Product Name</strong></label>

                        <input
                            type="text"
                            class="form-control"
                            name="product_name"
                            value="<?php echo e(request('product_name')); ?>"
                            placeholder="Enter product name">
                    </div>

                    
                    <div class="col-md-3 mb-3">
                        <label><strong>Customer Name</strong></label>

                        <input
                            type="text"
                            class="form-control"
                            name="customer_name"
                            value="<?php echo e(request('customer_name')); ?>"
                            placeholder="Enter customer name">
                    </div>

                    
                    <div class="col-md-3 mb-3">

                        <label><strong>Status</strong></label>

                        <select
                            class="form-control"
                            name="status">

                            <option value="">All Status</option>

                            <option value="sold"
                                <?php echo e(request('status')=='sold' ? 'selected':''); ?>>
                                Sold
                            </option>

                            <option value="sold_invoiced"
                                <?php echo e(request('status')=='sold_invoiced' ? 'selected':''); ?>>
                                Sold Invoiced
                            </option>

                            <option value="sold_invoiced_completed"
                                <?php echo e(request('status')=='sold_invoiced_completed' ? 'selected':''); ?>>
                                Completed
                            </option>

                            <option value="returned"
                                <?php echo e(request('status')=='returned' ? 'selected':''); ?>>
                                Returned
                            </option>

                        </select>

                    </div>

                    
                    <div class="col-md-3 mb-3">

                        <label><strong>Filter By</strong></label>

                        <select
                            id="filter_type"
                            class="form-control">

                            <option value="">Select</option>

                            <option value="date">
                                Date Range
                            </option>

                            <option value="month">
                                Month Range
                            </option>

                        </select>

                    </div>

                </div>

                

                <div
                    id="date_filter"
                    class="row d-none">

                    <div class="col-md-3 mb-3">

                        <label><strong>Start Date</strong></label>

                        <input
                            type="date"
                            class="form-control"
                            name="start_date"
                            value="<?php echo e(request('start_date')); ?>">

                    </div>

                    <div class="col-md-3 mb-3">

                        <label><strong>End Date</strong></label>

                        <input
                            type="date"
                            class="form-control"
                            name="end_date"
                            value="<?php echo e(request('end_date')); ?>">

                    </div>

                </div>

                

                <div
                    id="month_filter"
                    class="row d-none">

                    <div class="col-md-3 mb-3">

                        <label><strong>Start Month</strong></label>

                        <input
                            type="month"
                            class="form-control"
                            name="start_month"
                            value="<?php echo e(request('start_month')); ?>">

                    </div>

                    <div class="col-md-3 mb-3">

                        <label><strong>End Month</strong></label>

                        <input
                            type="month"
                            class="form-control"
                            name="end_month"
                            value="<?php echo e(request('end_month')); ?>">

                    </div>

                </div>

                

                <div class="row">

                    <div class="col-md-12 text-end">

                        <button
                            class="btn btn-primary"
                            type="submit">

                            <i class="fa fa-search"></i>
                            Search

                        </button>

                        <button
                            type="button"
                            id="clearForm"
                            class="btn btn-danger">

                            <i class="fa fa-times"></i>
                            Clear

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <!-- ========================= TABLE ========================= -->

    <div class="card">

        <div class="card-header">

            <h5 class="mb-0">

                <i class="fa fa-shopping-cart"></i>

                Sales Report

            </h5>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-striped">

                    <thead class="table">

                        <tr>

                            <th>#</th>
                            <th>Invoice No</th>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Sold Date</th>
                            <th>Discount</th>

                        </tr>

                    </thead>

                    <tbody>


        
                            <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        
                                <tr>
        
                                    <td><?php echo e($loop->iteration + ($sales->firstItem() - 1)); ?></td>
        
                                    <td>
                                        <?php echo e($sale->invoice_issued_id ?? 'N/A'); ?>

                                    </td>
        
                                    <td>
                                        <?php echo e($sale->products_tuli->product_name ?? 'N/A'); ?>

                                    </td>
        
                                    <td>
                                        <?php echo e($sale->customers_tuli->name ?? 'Walk In Customer'); ?>

                                    </td>
        
                                    <td>
                                        <?php echo e(number_format($sale->quantity,2)); ?>

                                    </td>
        
                                    <td>
                                        <?php echo e(number_format($sale->selling_price,2)); ?>

                                    </td>
        
                                    <td>
                                        <strong>
                                            <?php echo e(number_format($sale->quantity * $sale->selling_price,2)); ?>

                                        </strong>
                                    </td>
        
                                    <td>
        
                                        <?php if($sale->status=="sold"): ?>
                                            <span class="badge bg-primary">Sold</span>
        
                                        <?php elseif($sale->status=="sold_invoiced"): ?>
                                            <span class="badge bg-warning text-dark">
                                                Invoiced
                                            </span>
        
                                        <?php elseif($sale->status=="sold_invoiced_completed"): ?>
                                            <span class="badge bg-success">
                                                Completed
                                            </span>
        
                                        <?php elseif($sale->status=="returned"): ?>
                                            <span class="badge bg-danger">
                                                Returned
                                            </span>
        
                                        <?php else: ?>
                                            <span class="badge bg-secondary">
                                                <?php echo e($sale->status); ?>

                                            </span>
        
                                        <?php endif; ?>
        
                                    </td>
        
                                    <td>
                                        <?php echo e(\Carbon\Carbon::parse($sale->date_sold)->format('d/m/Y H:i')); ?>

                                    </td>
        
                                    <td>
        
                                        <?php if($sale->discount_amount>0): ?>
        
                                            <?php echo e(number_format($sale->discount_amount,2)); ?>

        
                                        <?php else: ?>
        
                                            -
        
                                        <?php endif; ?>
        
                                    </td>
        
                                </tr>
        
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        
                                <tr>
        
                                    <td colspan="10" class="text-center">
        
                                        No Sales Found
        
                                    </td>
        
                                </tr>
        
                            <?php endif; ?>
        
                        </tbody>
        
                        <tfoot>
        
                            <tr>
        
                                <th colspan="4" class="text-end">
                                    TOTAL
                                </th>
        
                                <th>
                                    <?php echo e(number_format($totalQty,2)); ?>

                                </th>
        
                                <th></th>
        
                                <th>
                                    <?php echo e(number_format($totalAmount,2)); ?> TZS
                                </th>
        
                                <th colspan="3"></th>
        
                            </tr>
        
                        </tfoot>
        
                    </table>
        
                    <div class="mt-3">
                        <?php echo e($sales->links()); ?>

                    </div>
        
                </div>
            </div>
        </div>

    <script>
        document.getElementById('filter_type').addEventListener('change', function () {
            document.getElementById('date_filter').classList.add('d-none');
            document.getElementById('month_filter').classList.add('d-none');
            if (this.value === 'date') document.getElementById('date_filter').classList.remove('d-none');
            if (this.value === 'month') document.getElementById('month_filter').classList.remove('d-none');
        });

        document.getElementById('clearForm').addEventListener('click', function () {
            document.getElementById('filterForm').reset();
            window.location.href = '<?php echo e(route('sales-report-tuli')); ?>';
        });

        document.getElementById('filterForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default submission

            let form = event.target;
            let formData = new FormData(form);
            let searchParams = new URLSearchParams();

            // Only add non-empty fields to the search query
            formData.forEach((value, key) => {
                if (value.trim() !== '') {
                    searchParams.append(key, value);
                }
            });

            // Redirect with clean query parameters
            window.location.href = form.action + '?' + searchParams.toString();
        });

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/admin/tuli_sales_management/sales-report.blade.php ENDPATH**/ ?>