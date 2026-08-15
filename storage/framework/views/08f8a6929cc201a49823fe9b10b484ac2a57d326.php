<?php $__env->startSection('title'); ?>
    <?php echo e(ucfirst(str_replace('-', ' ', Route::currentRouteName()))); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

    <?php $__env->startComponent('components.breadcrumb'); ?>

        <?php $__env->slot('breadcrumb_action_buttons'); ?>
            <li>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">
                    New <i class="icofont icofont-plus-circle"></i>
                </button>
            </li>
        <?php $__env->endSlot(); ?>

    <?php echo $__env->renderComponent(); ?>

    <div class="container-fluid">

        
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                <i class="icon-info-alt txt-danger"></i>
                <?php echo e($error); ?>

                <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <?php if(session('success')): ?>
            <div class="alert alert-success outline alert-dismissible fade show" role="alert">
                <i class="icofont icofont-check-circled"></i>
                <?php echo session('success'); ?>

                <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>


        <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('tuli-sales-management.quick-sale')->html();
} elseif ($_instance->childHasBeenRendered('IaHfJNp')) {
    $componentId = $_instance->getRenderedChildComponentId('IaHfJNp');
    $componentTag = $_instance->getRenderedChildComponentTagName('IaHfJNp');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('IaHfJNp');
} else {
    $response = \Livewire\Livewire::mount('tuli-sales-management.quick-sale');
    $html = $response->html();
    $_instance->logRenderedChild('IaHfJNp', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>

    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>

    <script src="<?php echo e(asset('assets/js/sweet-alert/sweetalert.min.js')); ?>"></script>

    <script>
        Livewire.on('InvoiceUpdated', () => {
            $('#newModal').modal('hide');

            setTimeout(() => {
                window.history.back();
            }, 500);
        });

        window.addEventListener('swal:modal', event => {
            swal({
                title: event.detail.message,
                text: event.detail.text,
                icon: event.detail.type,
                buttons: false,
                customClass: 'swal-wide'
            });
        });

        window.addEventListener('swal:confirm', event => {
            swal({
                title: event.detail.message,
                text: event.detail.text,
                icon: event.detail.type,
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    Livewire.emit('remove');
                }
            });
        });

        window.addEventListener('show-print-button', event => {
            const btn = document.createElement('a');
            btn.href = event.detail.url;
            btn.target = '_blank';
            btn.className = 'btn btn-outline-primary btn-xs mt-2';
            btn.innerHTML = 'Print Invoice <i class="icofont icofont-printer"></i>';

            const alertContainer = document.querySelector('.swal-modal');
            if (alertContainer) {
                const div = document.createElement('div');
                div.classList.add('mt-3');
                div.appendChild(btn);
                alertContainer.appendChild(div);
            }
        });
    </script>

<script>

    window.addEventListener('open-discount-modal', event => {
    
        let modal = new bootstrap.Modal(
            document.getElementById('discountModal')
        );
    
        modal.show();
    
    });
    
    
    
    window.addEventListener('close-discount-modal', event => {
    
        let modalElement =
        document.getElementById('discountModal');
    
    
        let modal =
        bootstrap.Modal.getInstance(modalElement);
    
    
        if(modal)
        {
            modal.hide();
        }
    
    });
    
    </script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/admin/tuli_sales_management/quick-sale.blade.php ENDPATH**/ ?>