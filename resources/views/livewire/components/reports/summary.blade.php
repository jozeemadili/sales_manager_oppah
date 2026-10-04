<div>
    <!-- End Day -->
    @if($dayLock)
    <div class="alert alert-warning d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <i class="icofont icofont-lock"></i>
            <strong>Day closed</strong> at {{ $dayLock['closed_at'] }} by {{ $dayLock['by'] }}.
            Mbao is <strong>view only</strong> until {{ $dayLock['locked_until'] }} (saa 12 asubuhi).
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('day-closure-pdf', $dayLock['id']) }}" target="_blank" class="btn btn-sm btn-primary"><i class="icofont icofont-file-pdf"></i> End Day PDF</a>
            @if(Auth::user()->hasFullAccess())
            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="reopenDay" onclick="return confirm('Re-open the day? Mbao users will be able to sell and edit again.')">Re-open Day</button>
            @endif
        </div>
    </div>
    @else
    <div class="d-flex justify-content-end mb-3">
        <button type="button" class="btn btn-danger" wire:click="previewEndDay">
            <i class="icofont icofont-power"></i> End Day
        </button>
    </div>
    @endif

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
            {{-- Hidden for now (requested 2026-10-04): period totals strip, Sales - (Inventory + Daily expenses) = Balance.
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
            --}}
            <figure class="highcharts-figure" wire:ignore>
                <div id="container_sales"></div>
            </figure>
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

        <div class="col-lg-12 mt-3">
            <div class="card income-card {{ $summary['balance_today_raw'] < 0 ? 'card-danger' : 'card-success' }} text-center">
                <div class="card-body">
                    <div class="round-box mb-2">
                        <i class="icofont icofont-chart-line" style="font-size: 40px;"></i>
                    </div>
                    <h5 class="{{ $summary['balance_today_raw'] < 0 ? 'text-danger' : '' }}">{{ $summary['balance_today'] }}</h5>
                    <p>Balance (Today)</p>
                    <small class="text-muted">
                        Paid today {{ $summary['paid_amount'] }}
                        &minus; Inventory expenses {{ $summary['expenses_today'] }}
                        &minus; Daily expenses {{ $summary['daily_today'] }}
                    </small>
                    <div class="mt-2">
                        <small>
                            Deposited today: <strong>{{ $summary['deposited_today'] }}</strong>
                            &middot; Left to deposit: <strong>{{ $summary['to_deposit'] }}</strong>
                        </small>
                    </div>
                    <div class="mt-2 d-flex justify-content-center gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary btn-sm" wire:click="openDeposit" @disabled($summary['to_deposit_raw'] <= 0 || ($dayLock && !Auth::user()->hasFullAccess()))>
                            <i class="icofont icofont-bank-alt"></i> Record Bank Deposit
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" wire:click="showDeposits">
                            <i class="icofont icofont-papers"></i> View Deposit Slips
                        </button>
                    </div>
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

    @if(count($dailyBreakdown))
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daily Expenses by Type (Today &mdash; {{ now()->format('d M Y') }})</h5>
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
                            <td class="text-end">{{ $summary['daily_today'] }}</td>
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
                            <span class="badge {{ $st['status'] === 'Pending' ? 'bg-warning text-dark' : ($st['status'] === 'Paid' ? 'bg-danger' : ($st['status'] === 'Quick Sale' ? 'bg-success' : 'bg-light text-dark border')) }}">
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
                        @if($detailType === 'unpaid_all')
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead><tr><th>Customer</th><th>Phone</th><th class="text-end">Invoices</th><th class="text-end">Oldest (days)</th><th class="text-end">Total</th><th class="text-end">Paid</th><th class="text-end">Remaining</th><th></th></tr></thead>
                            <tbody>
                                @forelse($detailCustomers as $cust)
                                <tr>
                                    <td><a href="{{ route('customer-statement', $cust['id']) }}" class="fw-semibold">{{ strtoupper($cust['name']) }}</a></td>
                                    <td><small>{{ $cust['phone'] ? '+255'.$cust['phone'] : '' }}</small></td>
                                    <td class="text-end">{{ $cust['invoices'] }}</td>
                                    <td class="text-end">{{ $cust['oldest_days'] }}</td>
                                    <td class="text-end">{{ number_format($cust['total'], 0) }}</td>
                                    <td class="text-end">{{ number_format($cust['paid'], 0) }}</td>
                                    <td class="text-end fw-semibold text-danger">{{ number_format($cust['remained'], 0) }}</td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('customer-statement', $cust['id']) }}" class="btn btn-outline-primary btn-xs">Statement</a>
                                        <a href="{{ route('customer-statement-pdf', $cust['id']) }}" class="btn btn-outline-danger btn-xs">PDF</a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="8" class="text-center text-muted py-4">No unpaid invoices.</td></tr>
                                @endforelse
                            </tbody>
                            @if(count($detailCustomers))
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="2">{{ count($detailCustomers) }} customers</td>
                                    <td class="text-end">{{ collect($detailCustomers)->sum('invoices') }}</td>
                                    <td></td>
                                    <td class="text-end">{{ number_format(collect($detailCustomers)->sum('total'), 0) }}</td>
                                    <td class="text-end">{{ number_format(collect($detailCustomers)->sum('paid'), 0) }}</td>
                                    <td class="text-end text-danger">{{ number_format(collect($detailCustomers)->sum('remained'), 0) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                        @elseif($detailType === 'paid_today')
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead><tr><th>Time</th><th>Receipt</th><th>Invoice</th><th>Customer</th><th>Channel</th><th>Received By</th><th class="text-end">Amount (TZS)</th></tr></thead>
                            <tbody>
                                @forelse($detailRows as $row)
                                <tr>
                                    <td>{{ $row['time'] }}</td>
                                    <td><small>{{ $row['receipt'] }}</small></td>
                                    <td>
                                        @if($row['invoice_id'])
                                            <a href="{{ route('invoice-preview', $row['invoice_id']) }}" target="_blank">#{{ $row['invoice_id'] }}</a>
                                        @else
                                            <span class="badge bg-success">Quick Sale</span>
                                        @endif
                                    </td>
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
                                    <td>
                                        @if($row['id'])
                                            <a href="{{ route('invoice-preview', $row['id']) }}" target="_blank">#{{ $row['id'] }}</a>
                                        @else
                                            &mdash;
                                        @endif
                                    </td>
                                    <td><small>{{ $row['date'] }}</small></td>
                                    <td class="text-end">{{ $row['age'] }}</td>
                                    <td>{{ strtoupper($row['customer']) }}</td>
                                    <td><span class="badge {{ $row['status'] === 'Pending' ? 'bg-warning text-dark' : ($row['status'] === 'Quick Sale' ? 'bg-success' : 'bg-light text-dark border') }}">{{ $row['status'] }}</span></td>
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

    <!-- Record bank deposit of today's balance -->
    <div class="modal fade" id="depositModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="icofont icofont-bank-alt"></i> Record Bank Deposit &mdash; {{ now()->format('d M Y') }}</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="saveDeposit">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label class="col-form-label">Amount to deposit (TZS)</label>
                            <input class="form-control fw-bold fs-5" type="text" value="{{ number_format((float) $depositAmount, 2) }}" readonly>
                            <small class="text-muted">Today's balance not yet deposited ({{ strtoupper($chartStoreName) }}).</small>
                            @error('depositAmount')<div class="text-danger small">{{ $message }}</div>@enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="col-form-label">Bank</label>
                                <input class="form-control" type="text" wire:model.defer="depositBank" list="depositBanks" placeholder="e.g. CRDB" required>
                                @error('depositBank')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="col-form-label">Account Number</label>
                                <input class="form-control" type="text" wire:model.defer="depositAccount" list="depositAccounts" required>
                                @error('depositAccount')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <datalist id="depositBanks">
                            @foreach(collect($depositSuggestions)->pluck('bank')->unique() as $bank)<option value="{{ $bank }}">@endforeach
                        </datalist>
                        <datalist id="depositAccounts">
                            @foreach(collect($depositSuggestions)->pluck('account')->unique() as $account)<option value="{{ $account }}">@endforeach
                        </datalist>
                        <div class="form-group">
                            <label class="col-form-label">Bank Slip(s)</label>
                            <input class="form-control" type="file" wire:model="depositSlips" multiple accept="image/*,application/pdf">
                            <div wire:loading wire:target="depositSlips" class="small text-muted mt-1">Uploading&hellip;</div>
                            @error('depositSlips')<div class="text-danger small">{{ $message }}</div>@enderror
                            @error('depositSlips.*')<div class="text-danger small">{{ $message }}</div>@enderror
                            <small class="text-muted d-block">JPG, PNG or PDF, up to 5 MB each.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary" type="submit" wire:loading.attr="disabled" wire:target="saveDeposit,depositSlips">Save Deposit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Deposits and their slips -->
    <div class="modal fade" id="depositsListModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="icofont icofont-papers"></i> Bank Deposits &mdash; {{ strtoupper($chartStoreName) }}</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead><tr><th>Recorded</th><th>For Day</th><th>Bank</th><th>Account</th><th class="text-end">Amount (TZS)</th><th>By</th><th>Status</th><th>Slips</th></tr></thead>
                            <tbody>
                                @forelse($depositList as $dep)
                                <tr>
                                    <td><small>{{ $dep['date'] }}</small></td>
                                    <td>{{ $dep['balance_date'] }}</td>
                                    <td>{{ strtoupper($dep['bank']) }}</td>
                                    <td>{{ $dep['account'] }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($dep['amount'], 2) }}</td>
                                    <td>{{ $dep['by'] }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $dep['status'] }}</span></td>
                                    <td>
                                        @foreach($dep['slips'] as $slip)
                                            <a href="{{ $slip['url'] }}" target="_blank" class="me-1">
                                                @if($slip['image'])
                                                    <img src="{{ $slip['url'] }}" width="48" height="48" style="object-fit: cover;" class="img-thumbnail">
                                                @else
                                                    <span class="badge bg-danger">PDF</span>
                                                @endif
                                            </a>
                                        @endforeach
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="8" class="text-center text-muted py-4">No deposits recorded yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- End Day preview / confirm -->
    <div class="modal fade" id="endDayModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="icofont icofont-power"></i> End Day &mdash; {{ strtoupper($chartStoreName) }} &mdash; {{ now()->format('d M Y') }}</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($endDayPreview)
                    @php $p = $endDayPreview; $f = fn ($v) => number_format((float) $v, 2); @endphp
                    <table class="table table-sm table-bordered">
                        <tr><th>Stock Value (Selling Price)</th><td class="text-end">{{ $f($p['stock_selling']) }}</td><th>Stock Value (Cost Price)</th><td class="text-end">{{ $f($p['stock_cost']) }}</td></tr>
                        <tr><th>Total Generated (Today)</th><td class="text-end">{{ $f($p['generated_today']) }} <small class="text-muted">({{ $p['lists']['generated_today']['count'] }})</small></td><th>Total Paid (Today)</th><td class="text-end">{{ $f($p['paid_today']) }} <small class="text-muted">({{ $p['lists']['paid_today']['count'] }})</small></td></tr>
                        <tr><th>Remaining Unpaid (Today)</th><td class="text-end">{{ $f($p['unpaid_today']) }} <small class="text-muted">({{ $p['lists']['unpaid_today']['count'] }})</small></td><th>Total Unpaid (All Time)</th><td class="text-end">{{ $f($p['unpaid_all']) }} <small class="text-muted">({{ count($p['lists']['unpaid_all']['customers']) }} customers)</small></td></tr>
                        <tr><th>Inventory Expenses (Today)</th><td class="text-end">{{ $f($p['inventory_expenses_today']) }}</td><th>Daily Expenses (Today)</th><td class="text-end">{{ $f($p['daily_expenses_today']) }} <small class="text-muted">({{ count($p['daily_entries']) }})</small></td></tr>
                        <tr class="table-success fw-bold"><th>Balance (Today)</th><td class="text-end">{{ $f($p['balance_today']) }}</td><th>Deposited to bank</th><td class="text-end">{{ $f($p['deposited_today']) }} <small class="text-muted">({{ count($p['deposits']) }} deposit(s))</small></td></tr>
                    </table>

                    @if($p['left_to_deposit'] > 0)
                    <div class="alert alert-danger py-2">
                        <strong>{{ $f($p['left_to_deposit']) }} TZS</strong> of today's balance has not been deposited to the bank. You can still end the day; the report will show it as not deposited.
                    </div>
                    @endif

                    @if(count($p['daily_entries']))
                    <h6 class="mt-3">Daily Expenses (Today)</h6>
                    <table class="table table-sm">
                        <thead><tr><th>Time</th><th>Expense</th><th>Description</th><th>By</th><th class="text-end">Amount</th></tr></thead>
                        <tbody>
                            @foreach($p['daily_entries'] as $e)
                            <tr><td>{{ $e['time'] }}</td><td>{{ strtoupper($e['type']) }}</td><td><small>{{ $e['description'] }}</small></td><td>{{ $e['by'] }}</td><td class="text-end">{{ $f($e['amount']) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif

                    @if(count($p['deposits']))
                    <h6 class="mt-3">Bank Deposits (Today)</h6>
                    <table class="table table-sm">
                        <thead><tr><th>Time</th><th>Bank</th><th>Account</th><th>By</th><th class="text-end">Amount</th><th>Slips</th></tr></thead>
                        <tbody>
                            @foreach($p['deposits'] as $d)
                            <tr><td>{{ $d['time'] }}</td><td>{{ strtoupper($d['bank']) }}</td><td>{{ $d['account'] }}</td><td>{{ $d['by'] }}</td><td class="text-end">{{ $f($d['amount']) }}</td><td>{{ count($d['slips']) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif

                    <div class="alert alert-warning mt-3 mb-0">
                        <strong>After you confirm:</strong> the End Day PDF opens (with the full invoice lists), and Mbao users become <strong>view only</strong>
                        &mdash; no selling, stock, invoices, expenses or deposits &mdash; until <strong>06:00 (saa 12 asubuhi)</strong>.
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" type="button" wire:click="confirmEndDay" wire:loading.attr="disabled" wire:target="confirmEndDay">
                        <i class="icofont icofont-check"></i> Confirm End Day
                    </button>
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

    function showModal(id) {
        const el = document.getElementById(id);
        (bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el)).show();
    }

    window.addEventListener('open-deposit-modal', () => showModal('depositModal'));
    window.addEventListener('open-end-day-modal', () => showModal('endDayModal'));
    window.addEventListener('end-day-message', (event) => alert(event.detail.text));
    window.addEventListener('end-day-closed', (event) => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('endDayModal'));
        if (modal) { modal.hide(); }
        window.open(event.detail.pdf, '_blank');
    });
    window.addEventListener('open-deposits-list', () => showModal('depositsListModal'));
    window.addEventListener('deposit-saved', (event) => {
        const el = document.getElementById('depositModal');
        const modal = bootstrap.Modal.getInstance(el);
        if (modal) { modal.hide(); }
        const text = 'Deposit of ' + event.detail.amount + ' TZS recorded.';
        if (typeof swal === 'function') { swal({ title: 'Saved', text: text, icon: 'success' }); } else { alert(text); }
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
