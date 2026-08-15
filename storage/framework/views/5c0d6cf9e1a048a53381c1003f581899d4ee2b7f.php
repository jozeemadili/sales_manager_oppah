<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startPush('css'); ?>
<?php $__env->stopPush(); ?>
    <?php $__env->startSection('content'); ?>
      <!-- Container-fluid starts-->
      <div class="container-fluid dashboard-default-sec">

        <div class="dashboard-section">
          <div class="dashboard-header mb-3">
              <h4><i class="icofont icofont-chart-bar-graph"></i> Monthly Sales TRuck</h4>
          </div>
          <div class="row">
              <div class="col-lg-12">
                  <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('components.reports.sales-chart-trucks-combined')->html();
} elseif ($_instance->childHasBeenRendered('ibNzjVm')) {
    $componentId = $_instance->getRenderedChildComponentId('ibNzjVm');
    $componentTag = $_instance->getRenderedChildComponentTagName('ibNzjVm');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('ibNzjVm');
} else {
    $response = \Livewire\Livewire::mount('components.reports.sales-chart-trucks-combined');
    $html = $response->html();
    $_instance->logRenderedChild('ibNzjVm', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
              </div>
          </div>
      </div>

       

     

      
      
      

      </div>
      <!-- Container-fluid Ends-->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/admin/dashboard/home-truck.blade.php ENDPATH**/ ?>