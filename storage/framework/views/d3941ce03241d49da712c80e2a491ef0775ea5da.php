
<div class="card">
    <div class="card-header">
      <div class="header-top d-sm-flex align-items-center">
        <h5>Invoice Status</h5>
      </div>
    </div>
    <div class="card-body p-0">
        <figure class="highcharts-figure">
            <div id="container_quotation_status"></div>
        </figure>
        
    </div>
  </div>

<?php $__env->startPush('css'); ?>
<style>
    #container_quotation_status
    {
        width: 100%;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
Highcharts.chart('container_quotation_status', 
{
chart: 
{
    type: 'pie',
    options3d: 
    {
        enabled: true,
        alpha: 45
    }
},
title: 
{
    text: '',
    align: 'left'
},
subtitle: 
{
    text: '',
    align: 'left'
},
plotOptions: 
{
    pie: {
        innerSize: 100,
        depth: 50
    }
},
series: [{
    name: 'Invoice Count',
    data: <?php echo json_encode($products_performance); ?>

}]
});

</script>
<?php $__env->stopPush(); ?>


<?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/livewire/components/reports/quotationstatus.blade.php ENDPATH**/ ?>