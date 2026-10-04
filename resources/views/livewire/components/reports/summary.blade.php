<div>
    <!-- Store Selection Dropdown -->
    <div class="mb-4 text-center">
        <label for="storeSelect"><strong>Select Store:</strong></label>
        <select id="storeSelect" wire:model="storeId" class="form-control w-auto d-inline-block">
            <option value="all">All Stores</option>
            @foreach($stores as $store)
                <option value="{{ $store->id }}">{{ $store->name }}</option>
            @endforeach
        </select>
    </div>
    <!-- Stock & Expenses -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card income-card card-secondary text-center">
                <div class="card-body">
                    <div class="round-box mb-2">
                        <i class="icofont icofont-abacus-alt" style="font-size: 40px;"></i>
                    </div>
                    <h5>{{ $summary['sumProduct'] }}</h5>
                    <p>Stock Value (Selling Price)</p>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card income-card card-primary text-center">
                <div class="card-body">
                    <div class="round-box mb-2">
                        <i class="icofont icofont-price" style="font-size: 40px;"></i>
                    </div>
                    <h5>{{ $summary['sumProductCost'] }}</h5>
                    <p>Stock Value (Cost Price)</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card income-card card-danger text-center">
                <div class="card-body">
                    <div class="round-box mb-2">
                        <i class="icofont icofont-money-bag" style="font-size: 40px;"></i>
                    </div>
                    <h5>{{ $summary['expenses_to_date'] }}</h5>
                    <p>Inventory Expenses (To Date)</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card income-card card-warning text-center">
                <div class="card-body">
                    <div class="round-box mb-2">
                        <i class="icofont icofont-calendar" style="font-size: 40px;"></i>
                    </div>
                    <h5>{{ $summary['expenses_month'] }}</h5>
                    <p>Inventory Expenses (This Month)</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card income-card card-secondary text-center">
                <div class="card-body">
                    <div class="round-box mb-2">
                        <i class="icofont icofont-clock-time" style="font-size: 40px;"></i>
                    </div>
                    <h5>{{ $summary['expenses_today'] }}</h5>
                    <p>Inventory Expenses (Today)</p>
                </div>
            </div>
        </div>
    </div>

    @if(count($expenseBreakdown))
    <div class="card">
        <div class="card-header">
            <h5>Inventory Expenses by Type (To Date)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Expense</th>
                            <th class="text-end">Entries</th>
                            <th class="text-end">Amount (TZS)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenseBreakdown as $row)
                        <tr>
                            <td>{{ strtoupper($row['name']) }}</td>
                            <td class="text-end">{{ $row['entries'] }}</td>
                            <td class="text-end">{{ number_format($row['total'], 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td>Total</td>
                            <td class="text-end">{{ collect($expenseBreakdown)->sum('entries') }}</td>
                            <td class="text-end">{{ $summary['expenses_to_date'] }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Daily (running) Expenses -->
    <div class="row">
        <div class="col-lg-4">
            <div class="card income-card card-warning text-center">
                <div class="card-body">
                    <div class="round-box mb-2">
                        <i class="icofont icofont-clock-time" style="font-size: 40px;"></i>
                    </div>
                    <h5>{{ $summary['daily_today'] }}</h5>
                    <p>Daily Expenses (Today)</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card income-card card-secondary text-center">
                <div class="card-body">
                    <div class="round-box mb-2">
                        <i class="icofont icofont-calendar" style="font-size: 40px;"></i>
                    </div>
                    <h5>{{ $summary['daily_month'] }}</h5>
                    <p>Daily Expenses (This Month)</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card income-card card-danger text-center">
                <div class="card-body">
                    <div class="round-box mb-2">
                        <i class="icofont icofont-calculator-alt-2" style="font-size: 40px;"></i>
                    </div>
                    <h5>{{ $summary['all_expenses_month'] }}</h5>
                    <p>Total Expenses This Month (Inventory + Daily)</p>
                </div>
            </div>
        </div>
    </div>

    @if(count($dailyBreakdown))
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daily Expenses by Type (This Month)</h5>
            <a href="{{ route('daily-expenses') }}" class="btn btn-outline-primary btn-xs">Open Daily Expenses</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Expense</th>
                            <th class="text-end">Entries</th>
                            <th class="text-end">Amount (TZS)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dailyBreakdown as $row)
                        <tr>
                            <td>{{ strtoupper($row['name']) }}</td>
                            <td class="text-end">{{ $row['entries'] }}</td>
                            <td class="text-end">{{ number_format($row['total'], 0) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td>Total</td>
                            <td class="text-end">{{ collect($dailyBreakdown)->sum('entries') }}</td>
                            <td class="text-end">{{ $summary['daily_month'] }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Sales vs Expenses Chart -->
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
    <!-- Summary Cards -->
    <div class="row mb-4">
        

        <div class="col-lg-3">
            <div class="card income-card card-secondary text-center">
                <div class="card-body">
                    <div class="round-box mb-2">
                        <i class="icofont icofont-abacus-alt" style="font-size: 40px;"></i>
                    </div>
                    <h5>{{ $summary['generated_amount'] }}</h5>
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
                    <h5>{{ $summary['paid_amount'] }}</h5>
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
                    <h5>{{ $summary['unpaid_amount'] }}</h5>
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
                    <h5>{{ $summary['unpaid_overall'] }}</h5>
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
                    <h5>{{ $summary['total_customers'] }}</h5>
                    <p>Total Customers</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card income-card card-success text-center">
                <div class="card-body">
                    <div class="round-box mb-2"><i class="icofont icofont-user-alt-3" style="font-size: 40px;"></i></div>
                    <h5>{{ $summary['active_customers'] }}</h5>
                    <p>Active Customers</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card income-card card-danger text-center">
                <div class="card-body">
                    <div class="round-box mb-2"><i class="icofont icofont-user-suited" style="font-size: 40px;"></i></div>
                    <h5>{{ $summary['inactive_customers'] }}</h5>
                    <p>Inactive Customers</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
#container_sales { height: 39vh; }
.highcharts-figure, .highcharts-data-table table { min-width: 510px; max-width: 900px; margin: 1em auto; }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/js/highcharts/highcharts.js') }}"></script>
<script src="{{ asset('assets/js/highcharts/highcharts-3d.js') }}"></script>
<script src="{{ asset('assets/js/highcharts/exporting.js') }}"></script>
<script src="{{ asset('assets/js/highcharts/accessibility.js') }}"></script>

<script>
document.addEventListener('livewire:load', function () {
    let salesData = @json($sales);
    let expensesData = @json($expensesChart);
    let dailyData = @json($dailyChart);

    function renderChart() {
        Highcharts.chart('container_sales', {
            chart: { type: 'column', options3d: { enabled: true, alpha: 10, beta: 25, depth: 70 } },
            title: { text: '', align: 'left' },
            plotOptions: { column: { depth: 25 } },
            xAxis: { categories: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'], labels: { skew3d: true, style: { fontSize: '16px' } } },
            yAxis: { title: { text: 'TZS', margin: 20 } },
            tooltip: { valueSuffix: ' TZS', shared: true },
            series: [
                { name: 'Total Sales', data: salesData },
                { name: 'Inventory Expenses', data: expensesData, color: '#e74c3c' },
                { name: 'Daily Expenses', data: dailyData, color: '#f39c12' }
            ]
        });
    }

    renderChart();

    // Fresh data arrives with each update (e.g. changing the store).
    Livewire.on('salesUpdated', (sales, expenses, daily) => {
        salesData = sales;
        expensesData = expenses;
        dailyData = daily;
        setTimeout(renderChart, 0); // after Livewire has finished patching the DOM
    });

    Livewire.hook('message.processed', () => { renderChart(); });
});
</script>
@endpush
