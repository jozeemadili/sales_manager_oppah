<?php $__env->startSection('title'); ?>
    <?php echo e(ucfirst(str_replace('-', ' ', Route::currentRouteName()))); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('breadcrumb_title'); ?>
            <h3><?php echo e(ucfirst(str_replace('-', ' ', Route::currentRouteName()))); ?></h3>
        <?php $__env->endSlot(); ?>

        <?php $__env->slot('breadcrumb_action_buttons'); ?>
            <?php if(Auth::user()->role == 'ADMIN'): ?>
                <li>
                    <!-- <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">
                        New <i class="icofont icofont-plus-circle"></i>
                    </button> -->
                </li>
            <?php endif; ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="container-fluid">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                <i class="icon-info-alt txt-danger"></i> <?php echo e($error); ?>

                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if($message = Session::get('success')): ?>
            <div class="alert alert-success outline alert-dismissible fade show" role="alert">
                <i class="icofont icofont-check-circled"></i> <?php echo $message; ?>

                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <br />
        <?php endif; ?>

        <!-- Filter Form -->
        <div class="card mb-3">
            <div class="card-body">
                <form id="filterForm" method="GET" action="<?php echo e(route('invoices')); ?>">
                    <div class="row g-3">
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
                                <option value="Partial_Paid" <?php echo e(request('status') == 'Partial_Paid' ? 'selected' : ''); ?>>Partial_Paid</option>
                            </select>
                        </div>
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="<?php echo e(route('download-invoices-csv', request()->all())); ?>" class="btn btn-success">
                                <i class="fa fa-download"></i> Download CSV
                            </a>
                            <!-- Clear Button -->
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
                    <?php if(count($Invoice) > 0): ?>
                    <?php
    $grandTotalQty = 0;
    $grandTotalAmount = 0;
?>

<?php
    $grandTotalAmount = 0;
    $grandTotalPaid = 0;
    $grandTotalRemained = 0;
?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Invoice No</th>
            <th>Type</th>
            <th>Customer</th>
            <th>Invoice Date</th>
            <th>Paid Date</th>
            <th>Paid By</th>
            <th>Total Quantity</th>
            <th>Total Amount</th>
            <th>Total Paid</th>
            <th>Total Remained</th>
            <th>Status</th>
            <th>Created By</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $Invoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                // Only sum the three columns
                $grandTotalAmount += $user->total_invoice_amount;
                $grandTotalPaid += $user->amount_paid;
                $grandTotalRemained += $user->amount_remained;
            ?>

            <tr>
                <th scope="row"><?php echo e($loop->index + 1); ?></th>
                <td><a href="#"><small>SF000<?php echo e($user->id); ?>/025</small></a></td>
                <td><?php echo e($user->status == 'Pending' ? 'PROFOMAL INVOICE' : 'INVOICE'); ?></td>
                <td><a href="#"><small><?php echo e($user->Customer->name); ?></small></a></td>
                <td><?php echo e(\Carbon\Carbon::parse($user->invoice_date)->format('d/m/Y H:i:s')); ?></td>
                <td>
                    <?php if(empty($user->date_paid)): ?>
                        ---
                    <?php else: ?>
                        <?php echo e(\Carbon\Carbon::parse($user->date_paid)->format('d/m/Y H:i:s')); ?>

                    <?php endif; ?>
                </td>
                <td><?php echo e($user->paid_by); ?></td>
                <td><?php echo e(number_format($user->invoice_items->sum('qty'), 2)); ?></td>
                <td><?php echo e(number_format($user->total_invoice_amount, 2)); ?></td>
                <td><?php echo e(number_format($user->amount_paid, 2)); ?></td>
                <td><?php echo e(number_format($user->amount_remained, 2)); ?></td>
                <td><?php echo e($user->status); ?></td>
                <td><?php echo e($user->User->first_name); ?></td>
                <td>
                    <a href="<?php echo e(route('invoice-download', ['id' => $user->id])); ?>" class="btn btn-outline-primary btn-xs">Print</a>
                    <a href="<?php echo e(route('invoice-preview', ['id' => $user->id])); ?>" class="btn btn-outline-primary btn-xs">View</a>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8"><strong>Grand Totals</strong></td>
            <td><strong><?php echo e(number_format($grandTotalAmount, 2)); ?></strong></td>
            <td><strong><?php echo e(number_format($grandTotalPaid, 2)); ?></strong></td>
            <td><strong><?php echo e(number_format($grandTotalRemained, 2)); ?></strong></td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>


<?php echo e($Invoice->links()); ?>


                    <?php else: ?>
                        <div class="alert alert-danger text-center">No Records Found Yet</div>
                    <?php endif; ?>
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

        // Form submit with clean URL
        document.getElementById('filterForm').addEventListener('submit', function (event) {
            let form = event.target;
            let formData = new FormData(form);
            let queryParams = new URLSearchParams();

            // Only append parameters with values
            formData.forEach(function(value, key) {
                if (value) {
                    queryParams.append(key, value);
                }
            });

            // Redirect to filtered URL
            window.location.href = form.action + '?' + queryParams.toString();
            event.preventDefault();  // Prevent normal form submission
        });

        // Clear button functionality
        document.getElementById('clearForm').addEventListener('click', function () {
            // Reset form fields
            document.getElementById('filterForm').reset();
            // Reset the filter type selection and show the default state
            document.getElementById('filter_type').value = '';
            document.getElementById('date_filter').classList.add('d-none');
            document.getElementById('month_filter').classList.add('d-none');

            // Redirect to the base URL with no query parameters
            window.location.href = '<?php echo e(route('invoices')); ?>';
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const filterForm = document.getElementById('filterForm');
            const tableRows = document.querySelectorAll('.invoice-row');

            filterForm.addEventListener('submit', function (event) {
                event.preventDefault(); // Prevent form submission
                filterTable(); // Call filtering function
            });

            function filterTable() {
                let invoiceNo = document.querySelector('input[name="invoice_no"]').value.toLowerCase();
                let customerName = document.querySelector('input[name="customer_name"]').value.toLowerCase();
                let controlNo = document.querySelector('input[name="control_no"]').value.toLowerCase();
                let status = document.querySelector('select[name="status"]').value.toLowerCase();

                tableRows.forEach(row => {
                    let rowInvoiceNo = row.getAttribute('data-invoice-no').toLowerCase();
                    let rowCustomerName = row.getAttribute('data-customer-name').toLowerCase();
                    let rowControlNo = row.getAttribute('data-control-no') ? row.getAttribute('data-control-no').toLowerCase() : "";
                    let rowStatus = row.getAttribute('data-status').toLowerCase();

                    // Check if each field matches the filter criteria
                    let matchesInvoiceNo = invoiceNo === "" || rowInvoiceNo.includes(invoiceNo);
                    let matchesCustomerName = customerName === "" || rowCustomerName.includes(customerName);
                    let matchesControlNo = controlNo === "" || rowControlNo.includes(controlNo);
                    let matchesStatus = status === "" || rowStatus === status;

                    // Show or hide rows based on filter match
                    if (matchesInvoiceNo && matchesCustomerName && matchesControlNo && matchesStatus) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                });
            }

            // Clear button functionality
            document.getElementById('clearForm').addEventListener('click', function () {
                document.getElementById('filterForm').reset();
                tableRows.forEach(row => row.style.display = ""); // Show all rows
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/admin/sales_management/invoice-reports.blade.php ENDPATH**/ ?>