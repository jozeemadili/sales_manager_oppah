@extends('layouts.admin.master')
@section('title')
{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}
@endsection
@push('css')
@endpush

@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}</h3>
    @endslot

    
    @slot('breadcrumb_action_buttons')
    <!-- <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#searchModal">Search <i class="icofont icofont-search-alt-1"></i></button></li> -->
      

    @endslot
    <li class="breadcrumb-item">{{ucfirst(explode('-', Route::currentRouteName())[0])}}</li>
    <li class="breadcrumb-item active">{{ucfirst(explode('-', Route::currentRouteName())[1])}}</li>
  @endcomponent
  
  <div class="container-fluid">
  @foreach ($errors->all() as $error)
                  <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                    <i class="icon-info-alt txt-danger"></i>
                        {{ $error }}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                  @endforeach
                  
                  @if($message = Session::get('success'))
                    <div class="alert alert-success outline alert-dismissible fade show" role="alert">
                    <i class="icofont icofont-check-circled"></i>
                        {!! $message !!}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                    <br />
					@endif
      <div class="row">
      <!DOCTYPE html>
<html lang="en">
    <head>
        
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
                                    <img src="{{ asset('/assets/images/logo/'.strtolower($quotation->company->short_form)).'.png' }}" data-holder-rendered="true" width="20%" />
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
                  <div class="text-gray-light"><b> BILL DETAILS </b></div>
                  @elseif($quotation->status == 'Confirmed')
                  <div class="text-gray-light"><b> INVOICE </b></div>
                  @elseif($quotation->status == 'Paid')
                  <div class="text-gray-light"><b> INVOICE </b></div>
                  @else
                  <div class="text-gray-light"><b> INVOICE </b></div>
                  @endif
                     
                </div>
                <div class="col invoice-details">
                <div><b>Invoice No : </b> SF000{{ $quotation->id }}/025 </div>
                <div><b>Date : </b> {{ $quotation->invoice_date->format('d M Y')}} </div>
                </div>

                <div class="row contacts">
               

                        <div class="col invoice-to">
                            <div class="text-gray-light"> BILLED TO :</div>
                            <h6 class="to"> {{ strtoupper($quotation->hotel_customer->name) }}  </h6>
                            <div class="address">+255{{ $quotation->hotel_customer->mobile }} | {{ $quotation->hotel_customer->email }}</div>
                            <div>Occupation: {{ $quotation->hotel_customer->occupation }} Tribe : {{ $quotation->hotel_customer->tribe }} </div>
                            <div>P.O.BOX {{ $quotation->hotel_customer->physical_addres }}</div>
                        </div>
<br/>

                    
<table border="1" cellspacing="0" cellpadding="0" style="font-size: 12px;">
    <thead>
        <tr>
            <th style="background-color: {{$quotation->company->color}}; color:white;">#</th>
            <th style="background-color: {{$quotation->company->color}}; color:white;">ROOM NAME</th>
            <th style="background-color: {{$quotation->company->color}}; color:white;">CATEGORY</th>
            <th style="background-color: {{$quotation->company->color}}; color:white;">AMMENTIES</th>
            <th style="background-color: {{$quotation->company->color}}; color:white;">START DATE</th>
            <th style="background-color: {{$quotation->company->color}}; color:white;">END DATE</th>
            <th style="background-color: {{$quotation->company->color}}; color:white;">NO DAYS</th>
            <th style="background-color: {{$quotation->company->color}}; color:white;">PRICE</th>
            <th style="background-color: {{$quotation->company->color}}; color:white;">TOTAL PRICE</th>
            <th style="background-color: {{$quotation->company->color}}; color:white;">ACTION</th>
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
                <td>
                    @if($quotation->status == 'Pending')
                        <a class='btn btn-outline-info btn-xs' data-bs-toggle="modal" data-bs-target="#editqtyModal{{$item->id}}">Edit Dates</a>
                        &nbsp;&nbsp;&nbsp;
                        <a class="btn btn-primary btn-xs" data-bs-toggle="modal" data-bs-target="#approvalModal{{$item->id}}"><i class='fa fa-tags'></i> Discount</a>
                    @else
                        <!-- <a class="btn btn-info btn-xs" data-bs-toggle="modal" data-bs-target="#returnToStock{{$item->id}}">Return</a> -->
                    @endif
                </td>
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
                    <table>
                        <tr style="font-size: 9px; ">
                            <td>
                            <div class="notices">
                        <div>NOTICE:</div>
                        <ul>
                          <li> <div class="notice">All Figures above are in Tanzania Shillings (TZS)</div></li>
                          <!-- <li><div class="notice">Terms Of Payment : 14 Days</div></li> -->
                        </ul>
                       
                        
                    </div>
                    <div class="notices">
                        <!-- <div>BANK INFROMATION:</div> -->
                        <!-- <ul>
                          <li><div class="notice">Beneficiary: SAFE EQUIPMENT AND GENERAL SUPPLY LIMITED</div></li>
                          <li><div class="notice">Beneficiary Bank: NMB BANK</div></li>
                          <li><div class="notice">Address of Bank: NMB HOUSE, DAR ES SALAAM</div></li>
                          <li><div class="notice">Swift Code: NMIBTZTZ</div></li>
                          <li><div class="notice">Account No: TZS 20110077201</div></li>
                          <li><div class="notice">USD 20110078202</div></li>
                        </ul> -->
                    </div>
                            </td>
                            <td style="text-align: right;">
    <div class="notice2">
    <!-- <div class="notice"><i>www.safe.co.tz</i></div>
    <div class="notice"><i>info@safe.co.tz</i></div>
    <div class="notice"><i>Dar Es Dalaam, Tanzania</i></div> -->
        
    </div>
</td>
                        </tr>
                      </table>
                   
                </main>
                <footer>
                A home Away From Home.
                </footer>
            </div>
            <div></div>
        </div>
    </div>
    

 
    </body>
</html>
       
      </div>
  </div>




  @push('scripts')
  @endpush
@endsection

@foreach($quotation->hotel_invoice_items as $item)
<div class="modal fade" id="editqtyModal{{$item->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Edit Quantity For ROOM : {{ $item->room->room_name }} IN ({{ $item->room->rooms_category->my_hotel->name }})</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                <form method="post" action="{{ Route('edit-dates-invoice-hotel') }}">
                    @csrf
                <div class="form-group">

                                <label class="col-form-label" > Enter Start Date </label>
                                <input class="form-control" type="date" value="{{ old('branch_name') }}" required  name="start_date">
                                <label class="col-form-label" > Enter End Date </label>
                                <input class="form-control" type="date" value="{{ old('branch_name') }}" required  name="end_date">
                            
                                <i>Previous Start Date was : <b>{{ \Carbon\Carbon::parse($item->start_date)->format('D-d-M-Y') }}</b></i>
                                <br/>
                                <i>Previous Start Date was : <b>{{ \Carbon\Carbon::parse($item->end_date)->format('D-d-M-Y') }}</b></i>
                                <input type="hidden" name="item_id"value="{{$item->id}}">
                                <input type="hidden" name="invoice_id"value="{{$invoice_id}}">
                    </div>
                    <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm & Edit Quantity</button>
                </form>
            </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

@foreach($quotation->hotel_invoice_items as $item)
<div class="modal fade" id="approvalModal{{$item->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Enter Discount Amount  For Room : {{ $item->room->room_name }}</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                <form method="post" action="{{ Route('edit-hotel-invoice-price') }}">
                    @csrf
                <div class="form-group">

                                <label class="col-form-label" > Enter New Amount </label>
                                <input class="form-control" type="text" value="{{ old('branch_name') }}" required  name="discount_amount">
                                <i>Previous Amount was {{ number_format($item->price, 2) }} TZS</i>
                                <input type="hidden" name="item_id"value="{{$item->id}}">
                                <input type="hidden" name="invoice_id"value="{{$invoice_id}}">
                    </div>
                    <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm & Discount</button>
                </form>
            </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach



 
