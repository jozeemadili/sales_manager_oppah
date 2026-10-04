<div class="ov-dashboard">

    @php
        $combinedRevenueToday = $mbao['generated_today'] + $hardware['generated_today'] + $trucks['revenue_today'];
        $combinedUnpaidOverall = $mbao['unpaid_overall'] + $hardware['unpaid_overall'];
        $combinedCustomers = $mbao['customers'] + $hardware['customers'];
    @endphp

    <!-- Grand totals strip -->
    <div class="ov-hero-stats">
        <div class="ov-hero-stat">
            <span class="ov-hero-stat__label">Revenue Today &middot; All Modules</span>
            <span class="ov-hero-stat__value">{{ number_format($combinedRevenueToday, 0) }}</span>
        </div>
        <div class="ov-hero-stat">
            <span class="ov-hero-stat__label">Unpaid Overall &middot; Mbao + Hardware</span>
            <span class="ov-hero-stat__value">{{ number_format($combinedUnpaidOverall, 0) }}</span>
        </div>
        <div class="ov-hero-stat">
            <span class="ov-hero-stat__label">Customers &middot; Mbao + Hardware</span>
            <span class="ov-hero-stat__value">{{ number_format($combinedCustomers, 0) }}</span>
        </div>
        <div class="ov-hero-stat">
            <span class="ov-hero-stat__label">Fleet Size</span>
            <span class="ov-hero-stat__value">{{ number_format($trucks['fleet_size'], 0) }}</span>
        </div>
    </div>

    <!-- Combined monthly performance -->
    <div class="ov-card ov-card--hero">
        <div class="ov-card__head">
            <h5>Combined Monthly Performance</h5>
            <span class="ov-card__hint">{{ now()->year }}</span>
        </div>
        <div id="ov_chart_combined" class="ov-chart ov-chart--hero"></div>
    </div>

    <!-- Per-module panels -->
    <div class="ov-modules">

        <div class="ov-card ov-card--module ov-card--mbao">
            <div class="ov-card__head">
                <div class="ov-card__title">
                    <span class="ov-icon ov-icon--mbao"><i class="icofont icofont-tree"></i></span>
                    <h5>Mbao</h5>
                </div>
            </div>
            <div class="ov-kpis">
                <div class="ov-kpi">
                    <span class="ov-kpi__value">{{ number_format($mbao['stock_value'], 0) }}</span>
                    <span class="ov-kpi__label">Stock Value</span>
                </div>
                <div class="ov-kpi">
                    <span class="ov-kpi__value">{{ number_format($mbao['generated_today'], 0) }}</span>
                    <span class="ov-kpi__label">Generated Today</span>
                </div>
                <div class="ov-kpi">
                    <span class="ov-kpi__value">{{ number_format($mbao['paid_today'], 0) }}</span>
                    <span class="ov-kpi__label">Paid Today</span>
                </div>
                <div class="ov-kpi">
                    <span class="ov-kpi__value">{{ number_format($mbao['unpaid_today'], 0) }}</span>
                    <span class="ov-kpi__label">Unpaid Today</span>
                </div>
            </div>
            <div class="ov-card__footer">
                <span>Unpaid overall: <strong>{{ number_format($mbao['unpaid_overall'], 0) }}</strong></span>
                <span>Customers: <strong>{{ number_format($mbao['customers'], 0) }}</strong></span>
            </div>
            <div id="ov_chart_mbao" class="ov-chart"></div>
        </div>

        <div class="ov-card ov-card--module ov-card--hardware">
            <div class="ov-card__head">
                <div class="ov-card__title">
                    <span class="ov-icon ov-icon--hardware"><i class="icofont icofont-industries"></i></span>
                    <h5>Hardware</h5>
                </div>
            </div>
            <div class="ov-kpis">
                <div class="ov-kpi">
                    <span class="ov-kpi__value">{{ number_format($hardware['stock_value'], 0) }}</span>
                    <span class="ov-kpi__label">Stock Value</span>
                </div>
                <div class="ov-kpi">
                    <span class="ov-kpi__value">{{ number_format($hardware['generated_today'], 0) }}</span>
                    <span class="ov-kpi__label">Generated Today</span>
                </div>
                <div class="ov-kpi">
                    <span class="ov-kpi__value">{{ number_format($hardware['paid_today'], 0) }}</span>
                    <span class="ov-kpi__label">Paid Today</span>
                </div>
                <div class="ov-kpi">
                    <span class="ov-kpi__value">{{ number_format($hardware['unpaid_today'], 0) }}</span>
                    <span class="ov-kpi__label">Unpaid Today</span>
                </div>
            </div>
            <div class="ov-card__footer">
                <span>Unpaid overall: <strong>{{ number_format($hardware['unpaid_overall'], 0) }}</strong></span>
                <span>Customers: <strong>{{ number_format($hardware['customers'], 0) }}</strong></span>
            </div>
            <div id="ov_chart_hardware" class="ov-chart"></div>
        </div>

        <div class="ov-card ov-card--module ov-card--truck">
            <div class="ov-card__head">
                <div class="ov-card__title">
                    <span class="ov-icon ov-icon--truck"><i class="icofont icofont-truck"></i></span>
                    <h5>Trucks / Logistics</h5>
                </div>
            </div>
            <div class="ov-kpis">
                <div class="ov-kpi">
                    <span class="ov-kpi__value">{{ number_format($trucks['fleet_size'], 0) }}</span>
                    <span class="ov-kpi__label">Fleet Size</span>
                </div>
                <div class="ov-kpi">
                    <span class="ov-kpi__value">{{ number_format($trucks['trips_this_month'], 0) }}</span>
                    <span class="ov-kpi__label">Trips This Month</span>
                </div>
                <div class="ov-kpi">
                    <span class="ov-kpi__value">{{ number_format($trucks['revenue_today'], 0) }}</span>
                    <span class="ov-kpi__label">Revenue Today</span>
                </div>
                <div class="ov-kpi">
                    <span class="ov-kpi__value">{{ number_format($trucks['balance_this_month'], 0) }}</span>
                    <span class="ov-kpi__label">Net Balance (Month)</span>
                </div>
            </div>
            <div class="ov-card__footer">
                <span>Bank deposits this month: <strong>{{ number_format($trucks['deposits_this_month'], 0) }}</strong></span>
            </div>
            <div id="ov_chart_truck" class="ov-chart"></div>
        </div>

    </div>
</div>

@push('css')
<style>
    .ov-dashboard {
        --ov-mbao: #2e7d32;
        --ov-mbao-soft: rgba(46, 125, 50, 0.1);
        --ov-hardware: #b8860b;
        --ov-hardware-soft: rgba(184, 134, 11, 0.1);
        --ov-truck: #1565c0;
        --ov-truck-soft: rgba(21, 101, 192, 0.1);
        --ov-ink: #1f2430;
        --ov-muted: #7a8296;
        --ov-line: #eef0f4;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }

    .ov-hero-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    .ov-hero-stat {
        background: #fff;
        border: 1px solid var(--ov-line);
        border-radius: 14px;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
    }

    .ov-hero-stat__label {
        font-size: 12.5px;
        color: var(--ov-muted);
        font-weight: 500;
        letter-spacing: .01em;
    }

    .ov-hero-stat__value {
        font-size: 24px;
        font-weight: 700;
        color: var(--ov-ink);
    }

    .ov-card {
        background: #fff;
        border: 1px solid var(--ov-line);
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(16, 24, 40, 0.05);
        padding: 22px;
        margin-bottom: 20px;
    }

    .ov-card__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }

    .ov-card__head h5 {
        margin: 0;
        font-weight: 700;
        color: var(--ov-ink);
        font-size: 16px;
    }

    .ov-card__hint {
        font-size: 12.5px;
        color: var(--ov-muted);
    }

    .ov-card__title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ov-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .ov-icon--mbao { background: var(--ov-mbao-soft); color: var(--ov-mbao); }
    .ov-icon--hardware { background: var(--ov-hardware-soft); color: var(--ov-hardware); }
    .ov-icon--truck { background: var(--ov-truck-soft); color: var(--ov-truck); }

    .ov-card--module { border-top: 3px solid transparent; }
    .ov-card--mbao { border-top-color: var(--ov-mbao); }
    .ov-card--hardware { border-top-color: var(--ov-hardware); }
    .ov-card--truck { border-top-color: var(--ov-truck); }

    .ov-modules {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .ov-kpis {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-bottom: 14px;
    }

    .ov-kpi {
        background: #fafbfc;
        border-radius: 12px;
        padding: 12px 14px;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .ov-kpi__value {
        font-size: 17px;
        font-weight: 700;
        color: var(--ov-ink);
    }

    .ov-kpi__label {
        font-size: 11.5px;
        color: var(--ov-muted);
    }

    .ov-card__footer {
        display: flex;
        justify-content: space-between;
        font-size: 12.5px;
        color: var(--ov-muted);
        border-top: 1px dashed var(--ov-line);
        padding-top: 10px;
        margin-bottom: 12px;
    }

    .ov-card__footer strong {
        color: var(--ov-ink);
    }

    .ov-chart {
        height: 180px;
    }

    .ov-chart--hero {
        height: 320px;
    }

    @media (max-width: 1200px) {
        .ov-hero-stats { grid-template-columns: repeat(2, 1fr); }
        .ov-modules { grid-template-columns: 1fr; }
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/js/highcharts/highcharts.js') }}"></script>
<script>
    (function () {
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        function renderOverviewCharts() {
            if (typeof Highcharts === 'undefined') return;

            Highcharts.chart('ov_chart_combined', {
                chart: { type: 'column', backgroundColor: 'transparent' },
                title: { text: '' },
                credits: { enabled: false },
                xAxis: { categories: months },
                yAxis: { title: { text: 'TZS' } },
                legend: { align: 'right', verticalAlign: 'top' },
                plotOptions: { column: { borderRadius: 4 } },
                colors: ['#2e7d32', '#b8860b', '#1565c0'],
                series: [
                    { name: 'Mbao', data: @json($mbaoSales) },
                    { name: 'Hardware', data: @json($hardwareSales) },
                    { name: 'Trucks (net balance)', data: @json($truckBalance) },
                ],
            });

            Highcharts.chart('ov_chart_mbao', {
                chart: { type: 'column', backgroundColor: 'transparent' },
                title: { text: '' },
                credits: { enabled: false },
                legend: { enabled: false },
                xAxis: { categories: months, labels: { style: { fontSize: '10px' } } },
                yAxis: { title: { text: null } },
                colors: ['#2e7d32'],
                series: [{ name: 'Sales', data: @json($mbaoSales) }],
            });

            Highcharts.chart('ov_chart_hardware', {
                chart: { type: 'column', backgroundColor: 'transparent' },
                title: { text: '' },
                credits: { enabled: false },
                legend: { enabled: false },
                xAxis: { categories: months, labels: { style: { fontSize: '10px' } } },
                yAxis: { title: { text: null } },
                colors: ['#b8860b'],
                series: [{ name: 'Sales', data: @json($hardwareSales) }],
            });

            Highcharts.chart('ov_chart_truck', {
                chart: { type: 'column', backgroundColor: 'transparent' },
                title: { text: '' },
                credits: { enabled: false },
                legend: { enabled: false },
                xAxis: { categories: months, labels: { style: { fontSize: '10px' } } },
                yAxis: { title: { text: null } },
                colors: ['#1565c0'],
                series: [{ name: 'Net balance', data: @json($truckBalance) }],
            });
        }

        // This script is pushed above the Livewire scripts in the layout, so
        // wait for the DOM (and Livewire) before rendering or touching it.
        function bootOverviewCharts() {
            renderOverviewCharts();
            if (window.Livewire) {
                Livewire.hook('message.processed', renderOverviewCharts);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', bootOverviewCharts);
        } else {
            bootOverviewCharts();
        }
    })();
</script>
@endpush
