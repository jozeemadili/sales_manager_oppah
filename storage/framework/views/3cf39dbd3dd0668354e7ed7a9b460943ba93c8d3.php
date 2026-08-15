<div class="card">
    <div class="card-header">
        <h5>
            <i class="icofont icofont-chart-bar-graph"></i>
            Monthly Truck Balance Comparison
        </h5>
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
    width: 100%;
    height: 45vh;
}

.highcharts-figure {
    margin: 0 auto;
    width: 100%;
    max-width: 100%;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('assets/js/highcharts/highcharts.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/highcharts/exporting.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/highcharts/accessibility.js')); ?>"></script>

<script>
function renderTruckChart() {

    const chart = Highcharts.chart('container_truck_contribution', {
        chart: {
            type: 'column',
            animation: true
        },

        title: { text: '' },

        subtitle: {
            text: 'Truck Balance Comparison — <?php echo e(date("Y")); ?>',
            align: 'left'
        },

        xAxis: {
            categories: [
                'Jan','Feb','Mar','Apr','May','Jun',
                'Jul','Aug','Sep','Oct','Nov','Dec'
            ],
            labels: {
                style: { fontSize: '14px' }
            }
        },

        yAxis: {
            min: 0,
            title: { text: 'Balance (TZS)' },
            // removed stackLabels (not needed for grouped bars)
        },

        tooltip: {
            shared: false,
            valueSuffix: ' TZS'
        },

        plotOptions: {
            column: {
                // ❗ NO STACKING → grouped columns
                stacking: null,
                dataLabels: { enabled: false },
                groupPadding: 0.05,   // smoother grouping responsiveness
                pointPadding: 0.02,
                borderWidth: 0
            }
        },

        series: <?php echo json_encode($chartData, JSON_UNESCAPED_SLASHES); ?>,

        responsive: {
            rules: [{
                condition: { maxWidth: 768 },
                chartOptions: {
                    xAxis: {
                        labels: { style: { fontSize: '12px' } }
                    },
                    plotOptions: {
                        column: {
                            groupPadding: 0.15,
                            pointPadding: 0.08
                        }
                    },
                    yAxis: {
                        // no stackLabels here either
                    }
                }
            }]
        }
    });

    chart.reflow();
}

// DOM ready
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(renderTruckChart, 150);
});

// Fully responsive on window resize
window.addEventListener('resize', function() {
    Highcharts.charts.forEach(chart => {
        if (chart) chart.reflow();
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH /Users/josephatwilliammadili/Desktop/new3/PROJECTS/New LARAVEL PROJECTS/sales_manager_portal/resources/views/livewire/components/reports/sales-chart-trucks-combined.blade.php ENDPATH**/ ?>