{{-- Timber Control Ledger (mirrors the paper form). Used by the End Day preview and PDF.
     Expects: $ledger (snapshot 'ledger'), $ledgerDate (Y-m-d), $tableClass, $closedBy (optional). --}}
@php
    $n = fn ($v) => number_format((float) $v, 0);
    $q = fn ($v) => rtrim(rtrim(number_format((float) $v, 2), '0'), '.');
    $sum = $ledger['summary'] ?? [];
    $tot = $ledger['totals'] ?? [];
@endphp
<div class="timber-ledger">
    <div style="text-align: center; font-weight: bold; font-size: 1.05em;">TIMBER CONTROL LEDGER</div>
    <div style="text-align: center; margin-bottom: 6px;">CONTROL LEDGER DATE: <strong>{{ \Carbon\Carbon::parse($ledgerDate)->format('d/m/Y') }}</strong></div>

    <table class="{{ $tableClass }}" style="width: 100%;">
        <thead>
            <tr>
                <th rowspan="2" style="vertical-align: bottom;">AINA YA MBAO</th>
                <th colspan="3" style="text-align: center;">MBAO</th>
                <th colspan="2" style="text-align: center;">THAMANI YA MBAO</th>
                <th colspan="2" style="text-align: center;">JUMLA YA BEI</th>
            </tr>
            <tr>
                <th class="num">ZILIZOSALIA</th>
                <th class="num">ZILIZOUZWA</th>
                <th class="num">ZILIZOBAKI</th>
                <th class="num">KUNUNUA</th>
                <th class="num">KUUZIA</th>
                <th class="num">KUNUNUA</th>
                <th class="num">KUUZIA</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ledger['rows'] ?? [] as $r)
            <tr>
                <td><strong>{{ $r['type'] }}</strong></td>
                <td class="num">{{ $q($r['opening']) }}</td>
                <td class="num">{{ $q($r['sold']) }}</td>
                <td class="num">{{ $q($r['remaining']) }}</td>
                <td class="num">@ {{ $n($r['unit_cost']) }}</td>
                <td class="num">@ {{ $n($r['unit_price']) }}</td>
                <td class="num">{{ $n($r['cost_total']) }}</td>
                <td class="num">{{ $n($r['sales_total']) }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align: center;">No timber stock or sales today.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td><strong>JUMLA KUU</strong></td>
                <td class="num">{{ $q($tot['opening'] ?? 0) }}</td>
                <td class="num">{{ $q($tot['sold'] ?? 0) }}</td>
                <td class="num">{{ $q($tot['remaining'] ?? 0) }}</td>
                <td class="num">&mdash;</td>
                <td class="num">&mdash;</td>
                <td class="num">{{ $n($tot['cost_total'] ?? 0) }}</td>
                <td class="num">{{ $n($tot['sales_total'] ?? 0) }}</td>
            </tr>
        </tfoot>
    </table>

    <table class="{{ $tableClass }} ledger-summary" style="width: 100%; margin-top: 8px;">
        <tbody>
            <tr><td>JUMLA KUU YA MAUZO</td><td class="num"><strong>TSH {{ $n($sum['mauzo'] ?? 0) }}</strong></td></tr>
            <tr><td>MATUMIZI JUMLA KUU</td><td class="num"><strong>TSH {{ $n($sum['matumizi_jumla'] ?? 0) }}</strong></td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;MATUMIZI YA NDANI <small>(Daily expenses)</small></td><td class="num">TSH {{ $n($sum['matumizi_ndani'] ?? 0) }}</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;MATUMIZI YA NJE <small>(Inventory expenses)</small></td><td class="num">TSH {{ $n($sum['matumizi_nje'] ?? 0) }}</td></tr>
            <tr><td>MADENI: YALIYOLIPWA <small>(paid today on invoices)</small></td><td class="num">TSH {{ $n($sum['madeni_yaliyolipwa'] ?? 0) }}</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;YASIYOLIPWA <small>(unpaid, today's invoices)</small></td><td class="num">TSH {{ $n($sum['madeni_yasiyolipwa'] ?? 0) }}</td></tr>
            <tr><td>KIASI KILICHOBAKI <small>(Balance today)</small></td><td class="num"><strong>TSH {{ $n($sum['kilichobaki'] ?? 0) }}</strong></td></tr>
            <tr><td>JUMLA KUU BANK ILIYOWEKWA</td><td class="num"><strong>TSH {{ $n($sum['bank_jumla'] ?? 0) }}</strong></td></tr>
            <tr><td>BANK ACC. / BANK NAME</td><td class="num">{{ implode(', ', $sum['bank_accounts'] ?? []) ?: '—' }}</td></tr>
            @if(!empty($closedBy))
            <tr><td>IMEANDALIWA NA (Prepared by)</td><td class="num">{{ strtoupper($closedBy) }}</td></tr>
            @endif
        </tbody>
    </table>
</div>
