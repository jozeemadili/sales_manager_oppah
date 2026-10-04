<div>
    <!-- Sales vs Expenses Chart -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0">Payment Trends &mdash; {{ strtoupper($chartStoreName) }}</h5>
            <div class="btn-group btn-group-sm" role="group" aria-label="Chart period">
                <button type="button" wire:click="$set('chartPeriod', 'week')" class="btn {{ $chartPeriod === 'week' ? 'btn-primary' : 'btn-outline-primary' }}">Week</button>
                <button type="button" wire:click="$set('chartPeriod', 'month')" class="btn {{ $chartPeriod === 'month' ? 'btn-primary' : 'btn-outline-primary' }}">Month</button>
                <button type="button" wire:click="$set('chartPeriod', 'year')" class="btn {{ $chartPeriod === 'year' ? 'btn-primary' : 'btn-outline-primary' }}">Year</button>
            </div>
        </div>
        <div class="card-body p-0">
            <!-- Period totals: Sales - (Inventory + Daily expenses) = Balance -->
            <div class="row g-2 text-center px-3 pt-3">
                <div class="col-6 col-md-3">
                    <div class="border rounded p-2">
                        <div class="small text-muted">Total Sales</div>
                        <div class="fw-bold">{{ number_format($chartTotals['sales'] ?? 0, 0) }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="border rounded p-2">
                        <div class="small text-muted">&minus; Inventory Expenses</div>
                        <div class="fw-bold text-danger">{{ number_format($chartTotals['inventory'] ?? 0, 0) }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="border rounded p-2">
                        <div class="small text-muted">&minus; Daily Expenses</div>
                        <div class="fw-bold text-warning">{{ number_format($chartTotals['daily'] ?? 0, 0) }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="border rounded p-2 {{ ($chartTotals['balance'] ?? 0) < 0 ? 'border-danger' : 'border-success' }}">
                        <div class="small text-muted">= Balance ({{ ucfirst($chartPeriod) }})</div>
                        <div class="fw-bold {{ ($chartTotals['balance'] ?? 0) < 0 ? 'text-danger' : 'text-success' }}">{{ number_format($chartTotals['balance'] ?? 0, 0) }}</div>
                    </div>
                </div>
            </div>
            <figure class="highcharts-figure" wire:ignore>
                <div id="container_sales"></div>
            </figure>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        

        <div class="col-lg-3">
            <div wire:click="showDetail('generated_today')" role="button" title="Click to see the list" class="summary-clickable card income-card card-secondary text-center">
                <div class="card-body">
                    <div class="round-box mb-2">
                        <i class="icofont icofont-abacus-alt" style="font-size: 40px;"></i>
                    </div>
                    <h5>{{ $summary['generated_amount'] }}</h5>
                    <p>Total Generated Amount (Today)</p>
                    <small class="text-muted"><i class="icofont icofont-list"></i> click to view list</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div wire:click="showDetail('paid_today')" role="button" title="Click to see the list" class="summary-clickable card income-card card-primary text-center">
                <div class="card-body">
                    <div class="round-box mb-2">
                        <i class="icofont icofont-tick-boxed" style="font-size: 40px;"></i>
                    </div>
                    <h5>{{ $summary['paid_amount'] }}</h5>
                    <p>Total Paid Amount (Today)</p>
                    <small class="text-muted"><i class="icofont icofont-list"></i> click to view list</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div wire:click="showDetail('unpaid_today')" role="button" title="Click to see the list" class="summary-clickable card income-card card-warning text-center">
                <div class="card-body">
                    <div class="round-box mb-2">
                        <i class="icofont icofont-exclamation-circle" style="font-size: 40px;"></i>
                    </div>
                    <h5>{{ $summary['unpaid_amount'] }}</h5>
                    <p>Remaining (Unpaid) Amount (Today)</p>
                    <small class="text-muted"><i class="icofont icofont-list"></i> click to view list</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 mt-3">
            <div wire:click="showDetail('unpaid_all')" role="button" title="Click to see the list" class="summary-clickable card income-card card-danger text-center">
                <div class="card-body">
                    <div class="round-box mb-2">
                        <i class="icofont icofont-bank-alt" style="font-size: 40px;"></i>
                    </div>
                    <h5>{{ $summary['unpaid_overall'] }}</h5>
                    <p>Total Unpaid Amount (All Time)</p>
                    <small class="text-muted"><i class="icofont icofont-list"></i> click to view list</small>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Fixed store notice -->
    <div class="alert alert-light border text-center mb-4">
        <i class="icofont icofont-store"></i>
        You are viewing stock from <strong>{{ strtoupper($chartStoreName) }}</strong> store
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

    <!-- Drill-down list for the invoice cards -->
    <div class="modal fade" id="summaryDetailModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">{{ $detailTitle }} &mdash; {{ strtoupper($chartStoreName) }}</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="d-flex flex-wrap gap-2 align-items-center p-3 border-bottom">
                        <span class="badge bg-primary fs-6">{{ number_format($detailCount) }} {{ $detailType === 'paid_today' ? 'payments' : 'invoices' }}</span>
                        <span class="badge bg-dark fs-6">Total: {{ number_format($detailTotal, 0) }} TZS</span>
                        @foreach($detailByStatus as $st)
                            <span class="badge {{ $st['status'] === 'Pending' ? 'bg-warning text-dark' : ($st['status'] === 'Paid' ? 'bg-danger' : 'bg-light text-dark border') }}">
                                {{ $st['status'] ?: 'No status' }}: {{ $st['n'] }} &middot; {{ number_format($st['amount'], 0) }}
                            </span>
                        @endforeach
                        @if($detailCount > count($detailRows))
                            <small class="text-muted">Showing the first {{ count($detailRows) }}.</small>
                        @endif
                    </div>
                    @if(collect($detailByStatus)->contains('status', 'Pending'))
                        <div class="alert alert-warning m-3 mb-0 py-2">
                            <strong>Pending</strong> invoices are quotations that were never confirmed, but they are counted in this total.
                        </div>
                    @endif
                    @if($detailType !== 'paid_today' && collect($detailByStatus)->contains('status', 'Paid'))
                        <div class="alert alert-danger m-3 mb-0 py-2">
                            Some invoices are marked <strong>Paid</strong> but still show an amount remaining &mdash; please check them.
                        </div>
                    @endif
                    <div class="table-responsive">
                        @if($detailType === 'paid_today')
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead><tr><th>Time</th><th>Receipt</th><th>Invoice</th><th>Customer</th><th>Channel</th><th>Received By</th><th class="text-end">Amount (TZS)</th></tr></thead>
                            <tbody>
                                @forelse($detailRows as $row)
                                <tr>
                                    <td>{{ $row['time'] }}</td>
                                    <td><small>{{ $row['receipt'] }}</small></td>
                                    <td><a href="{{ route('invoice-preview', $row['invoice_id']) }}" target="_blank">#{{ $row['invoice_id'] }}</a></td>
                                    <td>{{ strtoupper($row['customer']) }}</td>
                                    <td><small>{{ $row['channel'] }}</small></td>
                                    <td>{{ $row['by'] }}</td>
                                    <td class="text-end">{{ number_format($row['amount'], 0) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center text-muted py-4">No payments received today.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        @else
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead><tr><th>Invoice</th><th>Date</th><th class="text-end">Age (days)</th><th>Customer</th><th>Status</th><th class="text-end">Total</th><th class="text-end">Paid</th><th class="text-end">Remaining</th></tr></thead>
                            <tbody>
                                @forelse($detailRows as $row)
                                <tr class="{{ $row['status'] === 'Paid' && $row['remained'] > 0 ? 'table-danger' : '' }}">
                                    <td><a href="{{ route('invoice-preview', $row['id']) }}" target="_blank">#{{ $row['id'] }}</a></td>
                                    <td><small>{{ $row['date'] }}</small></td>
                                    <td class="text-end">{{ $row['age'] }}</td>
                                    <td>{{ strtoupper($row['customer']) }}</td>
                                    <td><span class="badge {{ $row['status'] === 'Pending' ? 'bg-warning text-dark' : 'bg-light text-dark border' }}">{{ $row['status'] }}</span></td>
                                    <td class="text-end">{{ number_format($row['total'], 0) }}</td>
                                    <td class="text-end">{{ number_format($row['paid'], 0) }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($row['remained'], 0) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="8" class="text-center text-muted py-4">No invoices.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
#container_sales { height: 460px; }
.summary-clickable { cursor: pointer; transition: transform .15s, box-shadow .15s; }
.summary-clickable:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,0,0,.12); }
.highcharts-figure { width: 100%; margin: 0; padding: 0 10px 10px; }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/js/highcharts/highcharts.js') }}"></script>
<script src="{{ asset('assets/js/highcharts/highcharts-3d.js') }}"></script>
<script src="{{ asset('assets/js/highcharts/exporting.js') }}"></script>
<script src="{{ asset('assets/js/highcharts/accessibility.js') }}"></script>

<script>
document.addEventListener('livewire:load', function () {
    // Month view has up to 31 days x 3 bars: smaller, angled labels so they fit.
    function labelOptions(count) {
        return count > 12
            ? { rotation: -45, style: { fontSize: '11px' } }
            : { rotation: 0, style: { fontSize: '13px' } };
    }

    // The chart container is wire:ignore'd: build the chart once and update it in place.
    const chart = Highcharts.chart('container_sales', {
        chart: { type: 'column' },
        title: { text: '', align: 'left' },
        plotOptions: { column: { groupPadding: 0.1, pointPadding: 0.02, borderWidth: 0 } },
        xAxis: { categories: @json($chartCategories), labels: labelOptions(@json($chartCategories).length) },
        yAxis: { title: { text: 'TZS', margin: 20 } },
        tooltip: { valueSuffix: ' TZS', shared: true },
        series: [
            { name: 'Total Sales', data: @json($sales) },
            { name: 'Inventory Expenses', data: @json($expensesChart), color: '#e74c3c' },
            { name: 'Daily Expenses', data: @json($dailyChart), color: '#f39c12' },
            { name: 'Balance', type: 'line', data: @json($balanceChart), color: '#27ae60', lineWidth: 3, marker: { radius: 3 }, zIndex: 5 }
        ]
    });

    window.addEventListener('open-summary-detail', () => {
        // This site ships Bootstrap 5.0.0-beta2, which has no getOrCreateInstance().
        const el = document.getElementById('summaryDetailModal');
        (bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el)).show();
    });

    Livewire.on('salesUpdated', (data) => {
        chart.xAxis[0].update({ categories: data.categories, labels: labelOptions(data.categories.length) }, false);
        chart.series[0].setData(data.sales, false);
        chart.series[1].setData(data.inventory, false);
        chart.series[2].setData(data.daily, false);
        chart.series[3].setData(data.balance, false);
        chart.redraw();
    });
});
</script>
@endpush
