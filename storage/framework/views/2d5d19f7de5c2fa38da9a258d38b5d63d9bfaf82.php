<div class="card">
    <div class="card-header">
      <div class="header-top d-sm-flex align-items-center">
        <h5><i class="icofont icofont-chart-bar-graph"></i> Truck Balance Contribution (Monthly)</h5>
      </div>
    </div>
  
    <div class="card-body p-0">
      <figure class="highcharts-figure">
          <div id="container_truck_contribution"></div>
      </figure>
    </div>
  </div>
  
  
  <?php $__env->startPush('css'); ?>
  <style>
  #container_truck_contribution {
      height: 40vh;
  }
  </style>
  <?php $__env->stopPush(); ?>
  
  
  <?php $__env->startPush('scripts'); ?>
  <script src="<?php echo e(asset('assets/js/highcharts/highcharts.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/js/highcharts/highcharts-3d.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/js/highcharts/exporting.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/js/highcharts/accessibility.js')); ?>"></script>
  
  <script>
  Highcharts.chart('container_truck_contribution', {
      chart: {
          type: 'column',
          options3d: {
              enabled: true,
              alpha: 7,
              beta: 20,
              depth: 60
          }
      },
  
      title: {
          text: '',
          align: 'left'
      },
  
      subtitle: {
          text: 'Monthly Balances by Truck (<?php echo e(date("Y")); ?>)',
          align: 'left'
      },
  
      xAxis: {
          categories: [
              'Jan','Feb','Mar','Apr','May','Jun',
              'Jul','Aug','Sep','Oct','Nov','Dec'
          ],
          labels: { skew3d: true }
      },
  
      yAxis: {
          title: { text: 'TZS' }
      },
  
      tooltip: {
          shared: true,
          valueSuffix: ' TZS'
      },
  
      plotOptions: {
          column: { depth: 25 }
      },
  
      series: <?php echo json_encode($chartData, JSON_UNESCAPED_SLASHES); ?>

  
  });
  </script>
  <?php $__env->stopPush(); ?>
  <?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/livewire/components/reports/sales-chart-trucks.blade.php ENDPATH**/ ?>