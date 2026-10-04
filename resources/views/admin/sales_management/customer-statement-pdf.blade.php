<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Statement - {{ $customer->name }}</title>
    @php
        $color = optional($company)->color ?: '#4c73aa';
        $logo = $company ? public_path('assets/images/logo/'.strtolower($company->short_form).'.png') : null;
    @endphp
    <style>
        @page { margin: 28px 32px 60px 32px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        .header { width: 100%; border-bottom: 2px solid {{ $color }}; padding-bottom: 8px; }
        .header td { vertical-align: middle; }
        .company-name { font-size: 15px; font-weight: bold; }
        .muted { color: #666; }
        .qr-block { margin-top: 22px; text-align: center; }
        .qr-caption { font-size: 8px; color: #666; margin-top: 2px; }
        h1 { text-align: center; font-size: 15px; letter-spacing: 1px; margin: 16px 0 4px; color: {{ $color }}; }
        .subtitle { text-align: center; margin-bottom: 14px; }
        .parties { width: 100%; margin-bottom: 12px; }
        .parties td { vertical-align: top; width: 50%; }
        .label { font-size: 9px; text-transform: uppercase; color: #666; }
        table.items { width: 100%; border-collapse: collapse; }
        table.items th { background: {{ $color }}; color: #fff; padding: 6px; font-weight: normal; text-align: left; }
        table.items td { padding: 6px; border-bottom: 1px solid #ddd; }
        table.items .num { text-align: right; white-space: nowrap; }
        table.items tfoot td { font-weight: bold; border-top: 2px solid #999; border-bottom: none; }
        .balance-box { margin-top: 14px; border: 2px solid #c0392b; padding: 10px; text-align: right; }
        .balance-box .amount { font-size: 16px; font-weight: bold; color: #c0392b; }
        .note { margin-top: 16px; padding: 8px 10px; border-left: 4px solid {{ $color }}; background: #f5f7fb; }
        .footer { position: fixed; bottom: -40px; left: 0; right: 0; text-align: center; font-size: 9px; color: #777; border-top: 1px solid #ccc; padding-top: 4px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td style="width: 90px;">
                @if($logo && file_exists($logo))
                    <img src="{{ $logo }}" style="width: 80px;">
                @endif
            </td>
            <td>
                <div class="company-name">{{ strtoupper(optional($company)->name) }}</div>
                @if($company)
                    <div class="muted">+255{{ $company->phone_number }} | {{ $company->email_address }}</div>
                    <div class="muted">P.O.BOX {{ $company->postal_address }} @if($company->tin) | TIN: {{ $company->tin }} @endif</div>
                @endif
            </td>
        </tr>
    </table>

    <h1>STATEMENT OF ACCOUNT &mdash; PAYMENT REMINDER</h1>
    <div class="subtitle muted">Date: {{ $generatedAt->format('d M Y') }}</div>

    <table class="parties">
        <tr>
            <td>
                <div class="label">Customer</div>
                <div><strong>{{ strtoupper($customer->name) }}</strong></div>
                @if($customer->phone)<div>+255{{ $customer->phone }}</div>@endif
                @if($customer->email)<div>{{ $customer->email }}</div>@endif
                @if($customer->tin)<div>TIN: {{ $customer->tin }}</div>@endif
                @if($customer->physical_addres)<div>{{ $customer->physical_addres }}</div>@endif
            </td>
            <td style="text-align: right;">
                <div class="label">Summary</div>
                <div>Unpaid invoices: <strong>{{ $invoices->count() }}</strong></div>
                <div>Total invoiced: <strong>{{ number_format($totals['total'], 2) }} TZS</strong></div>
                <div>Total paid: <strong>{{ number_format($totals['paid'], 2) }} TZS</strong></div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>#</th>
                <th>Invoice No</th>
                <th>Invoice Date</th>
                <th class="num">Total (TZS)</th>
                <th class="num">Paid (TZS)</th>
                <th class="num">Remaining (TZS)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ \App\Http\Controllers\Stock\CustomerStatementController::invoiceNumber($invoice) }}</td>
                <td>{{ optional($invoice->invoice_date)->format('d M Y') }}</td>
                <td class="num">{{ number_format($invoice->total_invoice_amount, 2) }}</td>
                <td class="num">{{ number_format($invoice->amount_paid, 2) }}</td>
                <td class="num">{{ number_format($invoice->amount_remained, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">TOTAL</td>
                <td class="num">{{ number_format($totals['total'], 2) }}</td>
                <td class="num">{{ number_format($totals['paid'], 2) }}</td>
                <td class="num">{{ number_format($totals['balance'], 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="balance-box">
        BALANCE DUE: <span class="amount">{{ number_format($totals['balance'], 2) }} TZS</span>
    </div>

    <div class="note">
        Dear {{ ucwords(strtolower($customer->name)) }},<br>
        Our records show the invoices above are not yet fully paid. Kindly settle the outstanding balance of
        <strong>{{ number_format($totals['balance'], 2) }} TZS</strong> at your earliest convenience.
        If you have already paid, please share the payment details so we can update your account.<br><br>
        <em>Tafadhali lipa kiasi kilichobaki cha <strong>{{ number_format($totals['balance'], 2) }} TZS</strong> mapema iwezekanavyo. Asante kwa ushirikiano wako.</em>
    </div>

    @isset($qrcode)
    <div class="qr-block">
        <img src="{{ $qrcode }}" style="width: 115px; height: 115px;">
        <div class="qr-caption">Scan to view this statement (valid until {{ $qrExpires->format('d M Y') }})</div>
    </div>
    @endisset

    <div class="footer">
        {{ optional($company)->name }} &middot; www.oppah01.co.tz &middot; Generated {{ $generatedAt->format('d M Y H:i') }}
    </div>
</body>
</html>
