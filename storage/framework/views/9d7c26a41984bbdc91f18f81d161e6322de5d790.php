<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startPush('css'); ?>
<style>
    .dashboard-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .dashboard-header h4 {
        font-weight: 600;
        color: #333;
    }

    .dashboard-section {
        margin-top: 25px;
    }

    .card {
        box-shadow: 0px 2px 8px rgba(0,0,0,0.05);
        border: none;
    }

    .income-card .card-body h5 {
        font-weight: 700;
        font-size: 1.5rem;
    }

    .income-card .card-body p {
        color: #777;
        font-size: 0.9rem;
    }

    .round-box {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 65px;
        height: 65px;
        margin-bottom: 10px;
    }

    hr.section-divider {
        border-top: 2px solid #eee;
        margin: 35px 0 20px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid dashboard-default-sec">

    <!-- 🧾 Summary Section -->
    <div class="dashboard-section">
        <div class="dashboard-header mb-3">
            <h4><i class="icofont icofont-chart-histogram"></i> Business Overview</h4>
        </div>
        <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('components.reports.summary')->html();
} elseif ($_instance->childHasBeenRendered('ABOFzKo')) {
    $componentId = $_instance->getRenderedChildComponentId('ABOFzKo');
    $componentTag = $_instance->getRenderedChildComponentTagName('ABOFzKo');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('ABOFzKo');
} else {
    $response = \Livewire\Livewire::mount('components.reports.summary');
    $html = $response->html();
    $_instance->logRenderedChild('ABOFzKo', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
    </div>

    <hr class="section-divider">

    <!-- 📊 Sales Chart Section -->
    <div class="dashboard-section">
        <div class="dashboard-header mb-3">
            <h4><i class="icofont icofont-chart-bar-graph"></i> Monthly Sales Performance</h4>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('components.reports.salescharts')->html();
} elseif ($_instance->childHasBeenRendered('zpXv01m')) {
    $componentId = $_instance->getRenderedChildComponentId('zpXv01m');
    $componentTag = $_instance->getRenderedChildComponentTagName('zpXv01m');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('zpXv01m');
} else {
    $response = \Livewire\Livewire::mount('components.reports.salescharts');
    $html = $response->html();
    $_instance->logRenderedChild('zpXv01m', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
            </div>
        </div>
    </div>

    <hr class="section-divider">

    <!-- 🧩 Invoice Status / Performance Section -->
    <div class="dashboard-section">
        <div class="dashboard-header mb-3">
            <h4><i class="icofont icofont-pie-chart"></i> Invoice Status Summary</h4>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('components.reports.quotationstatus')->html();
} elseif ($_instance->childHasBeenRendered('2STiH42')) {
    $componentId = $_instance->getRenderedChildComponentId('2STiH42');
    $componentTag = $_instance->getRenderedChildComponentTagName('2STiH42');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('2STiH42');
} else {
    $response = \Livewire\Livewire::mount('components.reports.quotationstatus');
    $html = $response->html();
    $_instance->logRenderedChild('2STiH42', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/admin/dashboard/home.blade.php ENDPATH**/ ?>