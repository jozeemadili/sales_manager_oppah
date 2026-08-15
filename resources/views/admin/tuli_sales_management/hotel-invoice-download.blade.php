<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="description" content="viho admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities." />
        <meta name="keywords" content="admin template, viho admin template, dashboard template, flat admin template, responsive admin template, web app" />
        <meta name="author" content="pixelstrap" />
        <link href="{{ public_path().'/assets/css/bootstrap.css' }}"  rel="stylesheet" id="bootstrap-css">
        <script src="{{ public_path().'/assets/js/bootstrap/bootstrap.min.js' }}"></script>
        <script src="{{ public_path().'/assets/js/jquery-3.5.1.min.js' }}"></script>
        <title>safeafrica</title>
        <style>
     #invoice {
  padding: 30px;
}

.invoice {
  position: relative;
  background-color: #FFF;
  min-height: 680px;
  padding: 15px;
}

.invoice header {
  padding: 10px 0;
  margin-bottom: 20px;
  border-bottom: 1px solid #223673;
}

.invoice .company-details {
  text-align: left;
}

.invoice .company-details .name {
  margin-top: 0;
  margin-bottom: 0;
}

.invoice .contacts {
  margin-bottom: 20px;
}

.invoice .invoice-to {
  text-align: left;
}

.qrcode {
  text-align: right;
}

.invoice .invoice-to .to {
  margin-top: 0;
  margin-bottom: 0;
}

.invoice .invoice-details {
  text-align: right;
}

.invoice .invoice-details .invoice-id {
  margin-top: 0;
  color: #223673;
}

.invoice main {
  padding-bottom: 50px;
}

.invoice main .thanks {
  margin-top: -110px;
  font-size: 2em;
  margin-bottom: 10px;
}

.invoice main .notices {
  padding-left: 6px;
  border-left: 6px solid #223673;
}
.invoice main .notice2 {
  padding-right: 6px;
  border-right: 6px solid #223673;
}

.invoice main .notices .notice {
  font-size: 1.2em;
}

.invoice table {
  width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
  margin-bottom: 20px;
}

.invoice table td,
.invoice table th {
  padding: 15px;
  /* border-bottom: 1px solid #fff; */
  background: none; /* Background removed */
}

.invoice table th {
  white-space: nowrap;
  font-weight: 400;
  font-size: 16px;
}

.invoice table td h3 {
  margin: 0;
  font-weight: 400;
  font-size: 1em;
}

.invoice table .qty,
.invoice table .total,
.invoice table .unit {
  text-align: center;
  font-size: 1.2em;
}

.invoice table .no {
  font-size: 1.2em;
}

.invoice table .unit {
  background: none; /* Background removed */
}

.invoice table .total {
  color: #fff;
}

.invoice table tbody tr:last-child td {
  /* border: none; */
}

.invoice table tfoot td {
  background: none; /* Background removed */
  border-bottom: none;
  white-space: nowrap;
  text-align: right;
  padding: 10px 20px;
  font-size: 1.2em;
  border-top: 1px solid #aaa;
}

.invoice table tfoot tr:first-child td {
  /* border-top: none; */
}

.invoice table tfoot tr:last-child td {
  font-size: 1.4em;
}

.invoice table tfoot tr td:first-child {
  /* border: none; */
}

.invoice footer {
  width: 100%;
  text-align: center;
  font-size: 12px;
  color: #777;
  border-top: 1px solid #aaa;
  padding: 8px 0;
}
.invoice .company-details-heading {
        text-align: center;
        font-size: 15px
        
        }
.invoice footer {
  position: absolute;
  bottom: 10px;
}

.invoice>div:last-child {
}

.th {
  background-color: white;
  color: black;
}

.page-break {
  page-break-after: always;
}

                        </style>
    </head>
    <body style="margin: 20px auto;">
      <div id="invoice">
        <div class="invoice overflow-auto">
            <div style="min-width: 600px">
                <header>
                    <div class="row">
                        <div class="col company-details">
                            <table>
                                <tr>
                                    <td>
                                    <img src="{{ public_path().'/assets/images/logo/'.strtolower($quotation->company->short_form).'.png' }}" data-holder-rendered="true" width="35%" />
                                    </td>
                                    <td>
                                    <div>{{ strtoupper($quotation->company->name) }} </div>
                                    <div> +255{{ $quotation->company->phone_number}}/+255763414192 | {{ $quotation->company->email_address }}</div>
                                    <div>P.O.BOX {{ $quotation->company->postal_address }} </div>
                                   
                                    </td>
                                </tr>
                            </table>
                        </div>
  
                    </div>
                </header>

                <main>
                <div class="col company-details-heading">
                @if($quotation->status == 'Pending')
                  <div class="text-gray-light"><b> BILL ITEMS </b></div>
                  @elseif($quotation->status == 'Confirmed')
                  <div class="text-gray-light"><b> INVOICE </b></div>
                  @elseif($quotation->status == 'Paid')
                  <div class="text-gray-light"><b> INVOICE </b></div>
                  @else
                  <div class="text-gray-light"><b> INVOICE </b></div>
                  @endif
                </div>
                <div class="col invoice-details">
                <div><b>Invoice No : </b> J000{{ $quotation->id }}/025 </div>
                <div><b>Date : </b> {{ $quotation->invoice_date->format('d M Y')}} </div>
                </div>

                <div class="row contacts">
               

                <div class="col invoice-to">
                            <div class="text-gray-light"> BILLED TO :</div>
                            <h6 class="to"> {{ strtoupper($quotation->hotel_customer->name) }}  </h6>
                            <div class="address">+255{{ $quotation->hotel_customer->mobile }} | {{ $quotation->hotel_customer->email }}</div>
                            <div>Occupation : {{ $quotation->hotel_customer->occupation }} Tribe : {{ $quotation->hotel_customer->tribe }} </div>
                            <div>P.O.BOX {{ $quotation->hotel_customer->physical_addres }}</div>
                        </div>
<br/>

<table border="1" cellspacing="0" cellpadding="0" style="font-size: 12px;">
    <thead>
        <tr>
            <th style="background-color: {{$quotation->company->color}}; color:white;">#</th>
            <th style="background-color: {{$quotation->company->color}}; color:white; font-size: 12px;">ROOM NAME</th>
            <th style="background-color: {{$quotation->company->color}}; color:white; font-size: 12px;">CATEGORY</th>
            <th style="background-color: {{$quotation->company->color}}; color:white; font-size: 12px;">AMMENTIES</th>
            <th style="background-color: {{$quotation->company->color}}; color:white; font-size: 12px;">START DATE</th>
            <th style="background-color: {{$quotation->company->color}}; color:white; font-size: 12px;">END DATE</th>
            <th style="background-color: {{$quotation->company->color}}; color:white; font-size: 12px;">NO DAYS</th>
            <th style="background-color: {{$quotation->company->color}}; color:white; font-size: 12px;">PRICE</th>
            <th style="background-color: {{$quotation->company->color}}; color:white; font-size: 12px;">TOTAL PRICE</th>
            
        </tr>
    </thead>
    <tbody>
        @php $total = 0; @endphp
        @foreach ($quotation->hotel_invoice_items as $index => $item)
            @php
                $start = \Carbon\Carbon::parse($item->start_date);
                $end = \Carbon\Carbon::parse($item->end_date);
                $days = $start->diffInDays($end);
                $subTotal = $days * $item->price;
                $total += $subTotal;
            @endphp
            <tr>
                <td>{{ $index + 1 }}.</td>
                <td>{{ $item->room->room_name }} ({{ $item->room->rooms_category->my_hotel->name }})</td> <!-- You can replace with actual room name if available -->
                <td>{{ $item->room->rooms_category->category_name }}</td>
                <td>{{ $item->room->rooms_category->ammenties }}</td>
                <td>{{ \Carbon\Carbon::parse($item->start_date)->format('Y-m-d') }}</td>
                <td>{{ \Carbon\Carbon::parse($item->end_date)->format('Y-m-d') }}</td>
                <td>{{ $days }}</td>
                <td>{{ number_format($item->price, 2) }}</td>
                <td>{{ number_format($subTotal, 2) }}</td>
                
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7">Subtotal</td>
            <td colspan="2">{{ number_format($total, 2, '.', ',') }} TZS</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="6">Tax %</td>
            <td colspan="2">0</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="6">GRAND TOTAL DAYS</td>
            <td colspan="2">{{ $quotation->hotel_invoice_items->sum(fn($i) => \Carbon\Carbon::parse($i->start_date)->diffInDays(\Carbon\Carbon::parse($i->end_date))) }}</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="6">GRAND TOTAL AMOUNT</td>
            <td colspan="2">{{ number_format($total, 2, '.', ',') }} TZS</td>
        </tr>
    </tfoot>
</table>
                        
                    </div>
                    <div class="col company-details-heading">
                                 @if (isset($qrcode))
                                  <img src="{{ $qrcode }}" />
                                @endif
                                <br/>
                                <b>Scan To validate </b>
                            </div>
                    <table>
                        <tr style="font-size: 9px; ">
                            <td>
                            <div class="notices">
                        <div>NOTICE:</div>
                        <ul>
                          <li> <div class="notice">All Figures above are in Tanzania Shillings (TZS)</div></li>
                          <li><div class="notice">Terms Of Payment : <b>Full Amount</b></div></li>
                        </ul>
                       
                        
                    </div>
                    
                            </td>
                            <td style="text-align: right;">
    <div class="notice2">
    <div class="notice"><i>www.jaja.co.tz</i></div>
    <div class="notice"><i>info@jaja.co.tz</i></div>
    <div class="notice"><i>Dar Es Dalaam, Tanzania</i></div>
        
    </div>
</td>
                        </tr>
                      </table>
                   
                </main>
                <footer>
                  A home Away From Home
                </footer>
            </div>
            <div></div>
        </div>
    </div>
    

 
    </body>
</html>
