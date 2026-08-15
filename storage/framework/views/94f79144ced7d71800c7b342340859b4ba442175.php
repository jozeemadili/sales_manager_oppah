<div>
    <!-- Store Selection Dropdown -->
    <div class="mb-4 text-center">
        <label for="storeSelect"><strong>Select Store:</strong></label>
        <select id="storeSelect" wire:model="storeId" class="form-control w-auto d-inline-block">
            <option value="all">All Stores</option>
            <?php $__currentLoopData = $stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($store->id); ?>"><?php echo e($store->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="col-lg-12">
        <div class="card income-card card-secondary text-center">
            <div class="card-body">
                <div class="round-box mb-2">
                    <i class="icofont icofont-abacus-alt" style="font-size: 40px;"></i>
                </div>
                <h5><?php echo e($summary['sumProduct']); ?></h5>
                <p>Stock Value</p>
            </div>
        </div>
    </div>
    <!-- Summary Cards -->
    <div class="row mb-4">
        

        <div class="col-lg-3">
            <div class="card income-card card-secondary text-center">
                <div class="card-body">
                    <div class="round-box mb-2">
                        <i class="icofont icofont-abacus-alt" style="font-size: 40px;"></i>
                    </div>
                    <h5><?php echo e($summary['generated_amount']); ?></h5>
                    <p>Total Generated Amount (Today)</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card income-card card-primary text-center">
                <div class="card-body">
                    <div class="round-box mb-2">
                        <i class="icofont icofont-tick-boxed" style="font-size: 40px;"></i>
                    </div>
                    <h5><?php echo e($summary['paid_amount']); ?></h5>
                    <p>Total Paid Amount (Today)</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card income-card card-warning text-center">
                <div class="card-body">
                    <div class="round-box mb-2">
                        <i class="icofont icofont-exclamation-circle" style="font-size: 40px;"></i>
                    </div>
                    <h5><?php echo e($summary['unpaid_amount']); ?></h5>
                    <p>Remaining (Unpaid) Amount (Today)</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 mt-3">
            <div class="card income-card card-danger text-center">
                <div class="card-body">
                    <div class="round-box mb-2">
                        <i class="icofont icofont-bank-alt" style="font-size: 40px;"></i>
                    </div>
                    <h5><?php echo e($summary['unpaid_overall']); ?></h5>
                    <p>Total Unpaid Amount (All Time)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Summary -->
    <hr class="mt-4 mb-3">
    <h5 class="text-center mb-3"><strong>Customer Summary</strong></h5>
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="card income-card card-primary text-center">
                <div class="card-body">
                    <div class="round-box mb-2"><i class="icofont icofont-users-alt-2" style="font-size: 40px;"></i></div>
                    <h5><?php echo e($summary['total_customers']); ?></h5>
                    <p>Total Customers</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card income-card card-success text-center">
                <div class="card-body">
                    <div class="round-box mb-2"><i class="icofont icofont-user-alt-3" style="font-size: 40px;"></i></div>
                    <h5><?php echo e($summary['active_customers']); ?></h5>
                    <p>Active Customers</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card income-card card-danger text-center">
                <div class="card-body">
                    <div class="round-box mb-2"><i class="icofont icofont-user-suited" style="font-size: 40px;"></i></div>
                    <h5><?php echo e($summary['inactive_customers']); ?></h5>
                    <p>Inactive Customers</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Chart -->
    <div class="card">
        <div class="card-header">
            <h5>Payment Trends</h5>
        </div>
        <div class="card-body p-0">
            <figure class="highcharts-figure">
                <div id="container_sales"></div>
            </figure>
        </div>
    </div>
</div>

<?php $__env->startPush('css'); ?>
<style>
#container_sales { height: 39vh; }
.highcharts-figure, .highcharts-data-table table { min-width: 510px; max-width: 900px; margin: 1em auto; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('assets/js/highcharts/highcharts.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/highcharts/highcharts-3d.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/highcharts/exporting.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/highcharts/accessibility.js')); ?>"></script>

<script>
document.addEventListener('livewire:load', function () {
    function renderChart() {
        Highcharts.chart('container_sales', {
            chart: { type: 'column', options3d: { enabled: true, alpha: 10, beta: 25, depth: 70 } },
            title: { text: '', align: 'left' },
            plotOptions: { column: { depth: 25 } },
            xAxis: { categories: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'], labels: { skew3d: true, style: { fontSize: '16px' } } },
            yAxis: { title: { text: 'TZS', margin: 20 } },
            tooltip: { valueSuffix: ' TZS' },
            series: [{ name: 'Total Sales', data: <?php echo json_encode($sales, 15, 512) ?> }]
        });
    }

    renderChart();

    Livewire.hook('message.processed', () => { renderChart(); });
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/livewire/components/reports/summary.blade.php ENDPATH**/ ?>