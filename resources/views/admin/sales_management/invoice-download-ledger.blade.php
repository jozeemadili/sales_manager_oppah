<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Truck Route Ledger</title>

    <link href="{{ public_path('assets/css/bootstrap.css') }}" rel="stylesheet">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 10px;
            padding: 0;
        }

        #invoice {
            padding: 10px;
        }

        .invoice {
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px 15px;
            position: relative;
        }

        /* HEADER */
        header {
            border-bottom: 1px solid #00af00;
            margin-bottom: 10px;
            padding-bottom: 5px;
            text-align: center;
        }

        header img {
            display: block;
            margin: 0 auto 5px;
        }

        header h3 {
            margin: 0;
            color: #00af00;
            font-size: 14px;
            text-transform: uppercase;
        }

        header p {
            margin: 0;
            font-size: 9px;
            line-height: 1.2;
        }

        /* TABLES */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        th, td {
            border: 1px solid #999;
            padding: 4px 5px;
        }

        th {
            background: #00af00;
            color: #fff;
            text-transform: uppercase;
            font-size: 9px;
        }

        .summary td {
            border: none;
            padding: 2px 0;
        }

        h4 {
            color: #00af00;
            margin: 8px 0 4px;
            font-size: 11px;
            border-bottom: 1px solid #00af00;
            display: inline-block;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .notices {
            margin-top: 5px;
            font-size: 9px;
            color: #555;
        }

        footer {
            text-align: center;
            font-size: 9px;
            color: #777;
            border-top: 1px solid #aaa;
            padding-top: 3px;
            margin-top: 8px;
        }

        hr {
            border: 0.5px solid #00af00;
            margin: 4px 0;
        }

        .qr-summary {
            text-align: center;
            margin-top: 3px;
        }

        .qr-summary img {
            margin-top: 4px;
        }

        .page-break {
            page-break-after: always;
        }

        @page {
            size: A4;
            margin: 0.6cm;
        }
    </style>
</head>

<body>
<div id="invoice">
    <div class="invoice">
        {{-- HEADER --}}
        <header>
            @if(isset($TrucksRoute->company))
            <img src="{{ asset('assets/images/logo/oppah.png') }}" width="60">
            @endif
            <h3>Oppah Logistics & Timber Supply</h3>
            <p>+255768952479 | {{ $TrucksRoute->company->email_address ?? 'info@oppah01.com' }}</p>
            <p>Dar Es Salaam, Kongewe Msikitini</p>
        </header>

        {{-- BASIC INFO --}}
        <table class="summary">
            <tr>
                <td><strong>Trip No:</strong></td>
                <td>{{ $TrucksRoute->trip_no }}</td>
                <td><strong>Trip Date:</strong></td>
                <td>{{ \Carbon\Carbon::parse($TrucksRoute->route_date)->format('d M Y') }}</td>
                <td><strong>Registered By:</strong></td>
                <td>{{ $TrucksRoute->user->first_name }}</td>
            </tr>
            <tr>
                <td><strong>Truck:</strong></td>
                <td>{{ $TrucksRoute->our_truck->plate_no ?? 'N/A' }}</td>
                <td><strong>Driver:</strong></td>
                <td>{{ $TrucksRoute->our_truck->driver->first_name ?? 'Unassigned' }}</td>
                <td><strong>Registered Date:</strong></td>
                <td>{{ $TrucksRoute->created_date }}</td>
            </tr>
        </table>
        <hr>

        {{-- CUSTOMERS --}}
        <h4>Customers Details</h4>
        <table class="summary">
            <tr>
                <td><strong>Going Customer:</strong></td>
                <td>{{ ucfirst($TrucksRoute->going_customer) }}</td>
                <td><strong>Fee:</strong></td>
                <td>{{ number_format($TrucksRoute->going_transport_fee ?? 0, 2) }} TZS</td>
            </tr>
            <tr>
                <td><strong>Return Customer:</strong></td>
                <td>{{ ucfirst($TrucksRoute->return_customer) }}</td>
                <td><strong>Fee:</strong></td>
                <td>{{ number_format($TrucksRoute->return_transport_fee ?? 0, 2) }} TZS</td>
            </tr>
        </table>

      

        {{-- ROUTE PLANS --}}

        <h4>Route Plans</h4>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>From</th>
                    <th>To</th>
                    <th>KM</th>
                    <th>Fuel (L)</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($RoutePlan as $index => $plan)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ ucfirst($plan->from_location) }}</td>
                        <td>{{ ucfirst($plan->to_location) }}</td>
                        <td class="text-center">{{ number_format($plan->distance_km, 0) }}</td>
                        <td class="text-center">{{ number_format($plan->fuel_litres, 0) }}</td>
                        <td class="text-center">{{ number_format($plan->amount_tsh, 0) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No route plans recorded</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- EXPENSES --}}
        <h4>Expenses</h4>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Expense</th>
                    <th>Amount</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ExpensesRecord as $i => $exp)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ strtoupper($exp->expense->e_name ?? 'N/A') }}</td>
                        <td class="text-center">{{ number_format($exp->amount_used, 0) }}</td>
                        <td>{{ strtoupper($exp->desr) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">No expenses recorded</td></tr>
                @endforelse
            </tbody>
        </table>

          {{-- FINANCIAL SUMMARY --}}
          @php
          $total_transport_fee = $TrucksRoute->total_fee;
          $total_expenses = $ExpensesRecord->sum('amount_used');
          $total_route_plan = $RoutePlan->sum('amount_tsh');
          $balance = $total_transport_fee - ($total_expenses + $total_route_plan);
      @endphp

      <h4>Financial Summary</h4>
      <table>
          <thead>
              <tr>
                  <th>Total Transport Fee</th>
                  <th>Total Route Fuel Cost</th>
                  <th>Total Expenses</th>
                  <th>Balance (Trip)</th>
                  <th>Balance (Month)</th>
                  
              </tr>
          </thead>
          <tbody>
              <tr>
                  <td class="text-center">{{ number_format($total_transport_fee, 2) }}</td>
                  <td class="text-center">{{ number_format($total_route_plan, 2) }}</td>
                  <td class="text-center">{{ number_format($total_expenses, 2) }}</td>
                  <td class="text-center"><b>{{ number_format($balance, 2) }}</b></td>
                  <td class="text-center"><b>{{ number_format($balanceRemainingMonth, 2) }}</b></td>
              </tr>
          </tbody>
      </table>

        {{-- NOTE --}}
        <div class="notices text-center">
            @if (isset($qrcode))
                <img src="{{ $qrcode }}" />
            @endif
            <br/>
            <b>Scan To Validate</b>
        </div>

        {{-- FOOTER --}}
        <footer>
            Smart Generation in Smart Business — www.oppah01.co.tz
        </footer>
    </div>
</div>
</body>
</html>
