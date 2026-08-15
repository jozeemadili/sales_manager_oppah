<?php $__env->startSection('title'); ?>
<?php echo e(ucfirst(str_replace('-',' ',Route::currentRouteName()))); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/date-picker.css')); ?>">
<style>
    .preview-box {
        border: 1px solid #ddd;
        padding: 10px;
        margin-top: 10px;
        display: inline-block;
        position: relative;
        margin-right: 10px;
    }
    .preview-box img {
        max-width: 120px;
        max-height: 120px;
    }
    .remove-btn {
        position: absolute;
        top: -8px;
        right: -8px;
        background: red;
        color: #fff;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 10px;
        border: none;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('breadcrumb_title'); ?>
        <h3><?php echo e(ucfirst(str_replace('-',' ',Route::currentRouteName()))); ?></h3>
    <?php $__env->endSlot(); ?>

    <?php $__env->slot('breadcrumb_action_buttons'); ?>
        <li>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">
                New <i class="icofont icofont-plus-circle"></i>
            </button>
        </li>
    <?php $__env->endSlot(); ?>

    <li class="breadcrumb-item"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[0])); ?></li>
    <li class="breadcrumb-item active"><?php echo e(ucfirst(explode('-', Route::currentRouteName())[1])); ?></li>
<?php echo $__env->renderComponent(); ?>


<div class="container-fluid">
<div class="row">
<div class="col-sm-12">

<?php if(session('success')): ?>
<div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

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
    <div class="card-header">
        <h5>Bank Deposits</h5>
    
        <h6 class="text-success mt-2">
            <strong>Total Deposits:</strong>
            <?php echo e(number_format($totalDeposits, 2)); ?>

        </h6>
    </div>
    

<div class="card-body table-responsive">

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Source / Origin </th>
                <th>Bank Deposited</th>
                <th>Amount Deposited</th>
                <th>Date Deposited</th>
                <th>status</th>
                <th>Slip</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php $__currentLoopData = $deposits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deposit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($deposit->id); ?></td>
                <td><?php echo e($deposit->deposit_origin); ?></td>
                <td><?php echo e($deposit->bank_name); ?></td>
                <td><?php echo e(number_format($deposit->deposited_amount, 2)); ?></td>
                <td><?php echo e($deposit->deposited_date->format('Y-m-d')); ?></td>
                <td><?php echo e($deposit->status); ?></td>
                
                <td>
                    <?php $__empty_1 = true; $__currentLoopData = $deposit->files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php if(Str::endsWith($file->file_path, ['jpg','jpeg','png'])): ?>
                            <a href="<?php echo e($file->file_url); ?>" target="_blank">
                                <img src="<?php echo e($file->file_url); ?>" width="70" class="img-thumbnail mb-1">
                            </a>
                        <?php else: ?>
                            <a href="<?php echo e($file->file_url); ?>" target="_blank" class="d-block">
                                📄 PDF File
                            </a>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <span class="text-muted">No files</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($deposit->status == 'Pending'): ?>
                    <div class="pull-right">
                        <a href="<?php echo Route('delete-unsubmited-deposit', ['id' => $deposit->id, 'status' => 'Inactive']); ?>" 
                           class="btn btn-outline-danger btn-xs"
                           onclick="return confirm('Are you sure you want to delete all inventory data?')">
                           Delete <i class="icofont icofont-ui-delete"></i>
                        </a>
                      </div>
                      <a href='<?php echo Route('send-approve-deposit', ['id' => $deposit->id]); ?>' class='btn btn-outline-primary btn-xs'>Send To Stock</a>
                    <?php endif; ?>
                </td>

            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <?php echo e($deposits->links()); ?>


</div>
</div>



<div class="modal fade" id="newModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="<?php echo e(route('bank-deposits.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                <div class="modal-header">
                    <h5 class="modal-title">Add Bank Deposit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Bank Name</label>
                            <input type="text" name="bank_name" value="CRDB" readonly class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Deposit Amount</label>
                            <input type="number" step="0.01" name="deposited_amount" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Deposit Origin</label>
                            <input type="text" name="deposit_origin" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Deposited Date</label>
                            <input type="date" name="deposited_date" class="form-control">
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label>Upload Slip(s)</label>
                            <input type="file" name="file_path[]" class="form-control" multiple accept="image/*,application/pdf" onchange="handleFiles(this.files)">
                        </div>

                        <div id="previewArea"></div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Save Deposit</button>
                </div>

            </form>

        </div>
    </div>
</div>


</div>
</div>
</div>

<?php $__env->stopSection(); ?>


<?php $__env->startPush('scripts'); ?>
<script>
let selectedFiles = [];

function handleFiles(files) {
    let previewArea = document.getElementById('previewArea');
    previewArea.innerHTML = "";

    selectedFiles = Array.from(files);

    selectedFiles.forEach((file, index) => {
        let box = document.createElement('div');
        box.classList.add('preview-box');

        let removeBtn = document.createElement('button');
        removeBtn.innerHTML = "x";
        removeBtn.classList.add('remove-btn');
        removeBtn.onclick = () => removeFile(index);
        box.appendChild(removeBtn);

        if (file.type.includes("image")) {
            let img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            box.appendChild(img);
        } else {
            let p = document.createElement('p');
            p.innerHTML = "📄 " + file.name;
            p.style.fontWeight = "bold";
            box.appendChild(p);
        }

        previewArea.appendChild(box);
    });
}

function removeFile(index) {
    selectedFiles.splice(index, 1);

    let input = document.querySelector('input[name="file_path[]"]');
    let dt = new DataTransfer();

    selectedFiles.forEach(f => dt.items.add(f));
    input.files = dt.files;

    handleFiles(selectedFiles);
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/admin/sales_management/bank-deposot-preview.blade.php ENDPATH**/ ?>