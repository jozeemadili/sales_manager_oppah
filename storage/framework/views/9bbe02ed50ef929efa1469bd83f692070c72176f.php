<?php $__env->startSection('title'); ?>
    Sales Report
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="icon-info-alt txt-danger"></i> <?php echo e($error); ?>

                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="icofont icofont-check-circled"></i> <?php echo session('success'); ?>

                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Filter Form -->
        <div class="card mb-3">
            <div class="card-body">
                <form id="filterForm" method="GET" action="<?php echo e(route('sales-report')); ?>">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label>Product Name:</label>
                            <input type="text" name="product_name" class="form-control" value="<?php echo e(request('product_name')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Customer Name:</label>
                            <input type="text" name="customer_name" class="form-control" value="<?php echo e(request('customer_name')); ?>">
                        </div>
                        <div class="col-md-3">
                            <label>Filter By:</label>
                            <select id="filter_type" class="form-control">
                                <option value="">Select Type</option>
                                <option value="date">Date</option>
                                <option value="month">Month</option>
                            </select>
                        </div>
                        <div id="date_filter" class="col-md-3 d-none">
                            <label>Start Date:</label>
                            <input type="date" name="start_date" class="form-control" value="<?php echo e(request('start_date')); ?>">
                            <label>End Date:</label>
                            <input type="date" name="end_date" class="form-control" value="<?php echo e(request('end_date')); ?>">
                        </div>
                        <div id="month_filter" class="col-md-3 d-none">
                            <label>Start Month:</label>
                            <input type="month" name="start_month" class="form-control" value="<?php echo e(request('start_month')); ?>">
                            <label>End Month:</label>
                            <input type="month" name="end_month" class="form-control" value="<?php echo e(request('end_month')); ?>">
                        </div>
                       
                        <div class="col-md-3">
                            <label>Status:</label>
                            <select name="status" class="form-control">
                                <option value="">Select Status</option>
                                <option value="Paid" <?php echo e(request('status') == 'Paid' ? 'selected' : ''); ?>>Paid</option>
                                <option value="Confirmed" <?php echo e(request('status') == 'Confirmed' ? 'selected' : ''); ?>>Confirmed</option>
                                <option value="Pending" <?php echo e(request('status') == 'Pending' ? 'selected' : ''); ?>>Pending</option>
                            </select>
                        </div>
                       
                      
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="<?php echo e(route('download-invoices')); ?>" class="btn btn-success">
                                <i class="fa fa-download"></i> Download CSV
                            </a>
                            <button type="button" id="clearForm" class="btn btn-outline-danger">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Invoice Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Customer</th>
                                <th>Invoice Date</th>
                                <th>Payment Date</th>
                                <th>Sold Quantity</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Date Paid</th>
                                <th>Paid By</th>
                               
                             
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                              <tr>
                                  <th scope="row"><?php echo e($loop->index + 1); ?></th>
                                  <td><?php echo e($sale->product->product_name); ?></td>
                                 
                                  <td><?php echo e($sale->invoice->customer->name ?? 'N/A'); ?></td>
                                 
                                  <td><?php echo e(\Carbon\Carbon::parse($sale->invoice->created_at)->format('d/m/Y H:i:s')); ?></td>
                                  <td><?php echo e(\Carbon\Carbon::parse($sale->invoice->paid_date)->format('d/m/Y H:i:s')); ?></td>
                                  <td><?php echo e(number_format($sale->qty, 2)); ?></td>
                                  <td><?php echo e(number_format($sale->qty * $sale->price, 2)); ?></td>
                                  <td><?php echo e($sale->status); ?></td>
                                  <?php if(empty($$sale->date_paid)): ?>
                                  
                                    <td></td>
                                    <?php else: ?> 
                                    <td><?php echo e(\Carbon\Carbon::parse($sale->date_paid)->format('d/m/Y H:i:s')); ?></td>
                                    <?php endif; ?>
                                  
                               
                                <td><?php echo e($sale->paid_by); ?></td>

                                 
                                  
                              </tr>
                             
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tfoot>
        <tr>
            <td colspan="5"><strong>Total</strong></td>
            <td><strong><?php echo e($totalQty); ?></strong></td>
            
            <td><strong><?php echo e(number_format($totalAmount, 2)); ?> TZS</strong></td>
        </tr>
    </tfoot>
                        </tbody>
                    </table>
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
            window.location.href = '<?php echo e(route('sales-report')); ?>';
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

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/admin/sales_management/sales-report.blade.php ENDPATH**/ ?>