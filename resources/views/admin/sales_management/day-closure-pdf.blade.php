<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>End Day Report {{ $s['business_date'] ?? '' }}</title>
    @php
        $color = optional($company)->color ?: '#4c73aa';
        $logo = $company ? public_path('assets/images/logo/'.strtolower($company->short_form).'.png') : null;
        $m = fn ($v) => number_format((float) $v, 2);
        $lists = $s['lists'] ?? [];
    @endphp
    <style>
        @page { margin: 26px 28px 50px 28px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9.5px; color: #222; }
        .header { width: 100%; border-bottom: 2px solid {{ $color }}; padding-bottom: 6px; }
        .header td { vertical-align: middle; }
        .company-name { font-size: 14px; font-weight: bold; }
        .muted { color: #666; }
        h1 { text-align: center; font-size: 14px; margin: 12px 0 2px; color: {{ $color }}; letter-spacing: 1px; }
        h2 { font-size: 11px; margin: 16px 0 5px; padding: 4px 6px; background: #eef2f8; border-left: 4px solid {{ $color }}; }
        .meta { text-align: center; margin-bottom: 8px; }
        table.t { width: 100%; border-collapse: collapse; }
        table.t th { background: {{ $color }}; color: #fff; padding: 4px 5px; font-weight: normal; text-align: left; }
        table.t td { padding: 4px 5px; border-bottom: 1px solid #e3e3e3; }
        table.t tfoot td { font-weight: bold; border-top: 1.5px solid #999; border-bottom: none; }
        .num, table.t th.num { text-align: right; white-space: nowrap; }
        table.kpi { width: 100%; border-collapse: collapse; }
        table.kpi td { padding: 5px 7px; border: 1px solid #ddd; width: 25%; }
        table.kpi .label { color: #666; font-size: 8.5px; }
        table.kpi .value { font-size: 11.5px; font-weight: bold; }
        .balance { border: 2px solid {{ ($s['balance_today'] ?? 0) < 0 ? '#c0392b' : '#27ae60' }}; padding: 8px; margin-top: 8px; }
        .balance .value { font-size: 15px; font-weight: bold; color: {{ ($s['balance_today'] ?? 0) < 0 ? '#c0392b' : '#27ae60' }}; }
        .warn { color: #c0392b; font-weight: bold; }
        .empty { color: #888; font-style: italic; padding: 4px 0; }
        .slip { display: inline-block; margin: 4px 6px 0 0; text-align: center; }
        .sign td { padding-top: 28px; width: 50%; }
        .footer { position: fixed; bottom: -32px; left: 0; right: 0; text-align: center; font-size: 8px; color: #777; border-top: 1px solid #ccc; padding-top: 3px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td style="width: 80px;">
                @if($logo && file_exists($logo))<img src="{{ $logo }}" style="width: 70px;">@endif
            </td>
            <td>
                <div class="company-name">{{ strtoupper(optional($company)->name) }}</div>
                @if($company)
                    <div class="muted">+255{{ $company->phone_number }} | {{ $company->email_address }} | P.O.BOX {{ $company->postal_address }}</div>
                @endif
            </td>
        </tr>
    </table>

    <h1>END OF DAY REPORT &mdash; {{ strtoupper($s['store'] ?? '') }}</h1>
    <div class="meta">
        Business date: <strong>{{ \Carbon\Carbon::parse($s['business_date'])->format('l, d M Y') }}</strong>
        &middot; Closed by <strong>{{ optional($closure->closer)->first_name }}</strong> at {{ $closure->closed_at->format('d M Y H:i') }}
        @if($closure->status === 'Reopened')
            <br><span class="warn">Re-opened by {{ optional($closure->reopener)->first_name }} at {{ optional($closure->reopened_at)->format('d M Y H:i') }}</span>
        @endif
    </div>

    <h2>Summary</h2>
    <table class="kpi">
        <tr>
            <td><div class="label">Stock Value (Selling Price)</div><div class="value">{{ $m($s['stock_selling']) }}</div></td>
            <td><div class="label">Stock Value (Cost Price)</div><div class="value">{{ $m($s['stock_cost']) }}</div></td>
            <td><div class="label">Total Generated (Today)</div><div class="value">{{ $m($s['generated_today']) }}</div></td>
            <td><div class="label">Total Paid (Today)</div><div class="value">{{ $m($s['paid_today']) }}</div></td>
        </tr>
        <tr>
            <td><div class="label">Remaining Unpaid (Today)</div><div class="value">{{ $m($s['unpaid_today']) }}</div></td>
            <td><div class="label">Total Unpaid (All Time)</div><div class="value">{{ $m($s['unpaid_all']) }}</div></td>
            <td><div class="label">Inventory Expenses (Today)</div><div class="value">{{ $m($s['inventory_expenses_today']) }}</div></td>
            <td><div class="label">Daily Expenses (Today)</div><div class="value">{{ $m($s['daily_expenses_today']) }}</div></td>
        </tr>
    </table>

    <div class="balance">
        <table style="width: 100%;"><tr>
            <td>
                <div class="label muted">BALANCE (TODAY) = Paid {{ $m($s['paid_today']) }} &minus; Inventory exp. {{ $m($s['inventory_expenses_today']) }} &minus; Daily exp. {{ $m($s['daily_expenses_today']) }}</div>
                <div class="value">{{ $m($s['balance_today']) }} TZS</div>
            </td>
            <td class="num">
                Deposited to bank: <strong>{{ $m($s['deposited_today']) }}</strong><br>
                Left to deposit: <strong class="{{ ($s['left_to_deposit'] ?? 0) > 0 ? 'warn' : '' }}">{{ $m($s['left_to_deposit']) }}</strong>
            </td>
        </tr></table>
    </div>

    {{-- Invoice lists (same as the dashboard click-through lists) --}}
    @foreach([
        'generated_today' => 'Total Generated Amount (Today)',
        'paid_today' => 'Total Paid Amount (Today)',
        'unpaid_today' => 'Remaining (Unpaid) Amount (Today)',
    ] as $key => $title)
        @php $list = $lists[$key] ?? ['rows' => [], 'total' => 0, 'count' => 0]; @endphp
        <h2>{{ $title }} &mdash; {{ $m($list['total']) }} ({{ $list['count'] }})</h2>
        @if(count($list['rows']))
            @if($key === 'paid_today')
            <table class="t">
                <thead><tr><th>Time</th><th>Receipt</th><th>Invoice</th><th>Customer</th><th>Channel</th><th>Received By</th><th class="num">Amount</th></tr></thead>
                <tbody>
                    @foreach($list['rows'] as $r)
                    <tr>
                        <td>{{ $r['time'] }}</td><td>{{ $r['receipt'] }}</td>
                        <td>{{ $r['invoice_id'] ? 'OP000'.$r['invoice_id'].'/025' : 'Quick Sale' }}</td>
                        <td>{{ strtoupper($r['customer']) }}</td><td>{{ $r['channel'] }}</td><td>{{ $r['by'] }}</td>
                        <td class="num">{{ $m($r['amount']) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot><tr><td colspan="6">Total</td><td class="num">{{ $m($list['total']) }}</td></tr></tfoot>
            </table>
            @else
            <table class="t">
                <thead><tr><th>Invoice</th><th>Date</th><th>Customer</th><th>Status</th><th class="num">Total</th><th class="num">Paid</th><th class="num">Remaining</th></tr></thead>
                <tbody>
                    @foreach($list['rows'] as $r)
                    <tr>
                        <td>{{ $r['id'] ? 'OP000'.$r['id'].'/025' : '—' }}</td><td>{{ $r['date'] }}</td>
                        <td>{{ strtoupper($r['customer']) }}</td><td>{{ $r['status'] }}</td>
                        <td class="num">{{ $m($r['total']) }}</td><td class="num">{{ $m($r['paid']) }}</td><td class="num">{{ $m($r['remained']) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot><tr><td colspan="4">Total</td>
                    <td class="num">{{ $m(collect($list['rows'])->sum('total')) }}</td>
                    <td class="num">{{ $m(collect($list['rows'])->sum('paid')) }}</td>
                    <td class="num">{{ $m(collect($list['rows'])->sum('remained')) }}</td></tr></tfoot>
            </table>
            @endif
        @else
            <div class="empty">None.</div>
        @endif
    @endforeach

    @php $unpaid = $lists['unpaid_all'] ?? ['customers' => [], 'total' => 0, 'count' => 0]; @endphp
    <h2>Total Unpaid Amount (All Time) &mdash; {{ $m($unpaid['total']) }} ({{ $unpaid['count'] }} invoices)</h2>
    @if(count($unpaid['customers']))
    <table class="t">
        <thead><tr><th>Customer</th><th>Phone</th><th class="num">Invoices</th><th class="num">Oldest (days)</th><th class="num">Total</th><th class="num">Paid</th><th class="num">Remaining</th></tr></thead>
        <tbody>
            @foreach($unpaid['customers'] as $c)
            <tr>
                <td>{{ strtoupper($c['name']) }}</td><td>{{ $c['phone'] ? '+255'.$c['phone'] : '' }}</td>
                <td class="num">{{ $c['invoices'] }}</td><td class="num">{{ $c['oldest_days'] }}</td>
                <td class="num">{{ $m($c['total']) }}</td><td class="num">{{ $m($c['paid']) }}</td><td class="num">{{ $m($c['remained']) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot><tr><td colspan="4">{{ count($unpaid['customers']) }} customers</td>
            <td class="num">{{ $m(collect($unpaid['customers'])->sum('total')) }}</td>
            <td class="num">{{ $m(collect($unpaid['customers'])->sum('paid')) }}</td>
            <td class="num">{{ $m(collect($unpaid['customers'])->sum('remained')) }}</td></tr></tfoot>
    </table>
    @else
        <div class="empty">None.</div>
    @endif

    <h2>Daily Expenses (Today) &mdash; {{ $m($s['daily_expenses_today']) }}</h2>
    @if(count($s['daily_by_type'] ?? []))
    <table class="t" style="margin-bottom: 6px;">
        <thead><tr><th>Expense Type</th><th class="num">Entries</th><th class="num">Amount</th></tr></thead>
        <tbody>
            @foreach($s['daily_by_type'] as $t)
            <tr><td>{{ strtoupper($t['name']) }}</td><td class="num">{{ $t['entries'] }}</td><td class="num">{{ $m($t['total']) }}</td></tr>
            @endforeach
        </tbody>
    </table>
    <table class="t">
        <thead><tr><th>Time</th><th>Expense</th><th>Description</th><th>Recorded By</th><th class="num">Amount</th></tr></thead>
        <tbody>
            @foreach($s['daily_entries'] as $e)
            <tr><td>{{ $e['time'] }}</td><td>{{ strtoupper($e['type']) }}</td><td>{{ $e['description'] }}</td><td>{{ $e['by'] }}</td><td class="num">{{ $m($e['amount']) }}</td></tr>
            @endforeach
        </tbody>
    </table>
    @else
        <div class="empty">No daily expenses recorded today.</div>
    @endif

    <h2>Bank Deposits (Today) &mdash; {{ $m($s['deposited_today']) }}</h2>
    @if(count($s['deposits'] ?? []))
    <table class="t">
        <thead><tr><th>Time</th><th>Bank</th><th>Account</th><th>By</th><th class="num">Amount</th></tr></thead>
        <tbody>
            @foreach($s['deposits'] as $d)
            <tr><td>{{ $d['time'] }}</td><td>{{ strtoupper($d['bank']) }}</td><td>{{ $d['account'] }}</td><td>{{ $d['by'] }}</td><td class="num">{{ $m($d['amount']) }}</td></tr>
            @endforeach
        </tbody>
    </table>
    <div>
        @foreach($s['deposits'] as $d)
            @foreach($d['slips'] as $slip)
                <div class="slip">
                    @if(!empty($slip['file']))
                        <img src="{{ $slip['file'] }}" style="max-width: 160px; max-height: 210px; border: 1px solid #ccc;">
                    @else
                        <div style="border: 1px solid #ccc; padding: 18px 10px;">PDF slip</div>
                    @endif
                    <div class="muted">{{ strtoupper($d['bank']) }} &middot; {{ $m($d['amount']) }}</div>
                </div>
            @endforeach
        @endforeach
    </div>
    @else
        <div class="empty">No bank deposit recorded today.</div>
    @endif
    @if(($s['left_to_deposit'] ?? 0) > 0)
        <div class="warn" style="margin-top: 6px;">Not yet deposited: {{ $m($s['left_to_deposit']) }} TZS</div>
    @endif

    <table class="sign" style="width: 100%;">
        <tr>
            <td>Closed by: {{ optional($closure->closer)->first_name }} ____________________</td>
            <td>Checked by: ______________________________</td>
        </tr>
    </table>

    <div class="footer">
        {{ optional($company)->name }} &middot; End Day #{{ $closure->id }} &middot; Report generated {{ now()->format('d M Y H:i') }}
    </div>
</body>
</html>
