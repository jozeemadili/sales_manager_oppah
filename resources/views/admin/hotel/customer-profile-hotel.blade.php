@extends('layouts.admin.master')
@section('title')
{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/date-picker.css') }}">
@endpush

@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}</h3>
    @endslot

    
    @slot('breadcrumb_action_buttons')

        <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">New <i class="icofont icofont-plus-circle"></i></button></li>


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
        @if(isset($Customers))
          <div class="col-sm-12">
              <div class="card">
                  <div class="card-body">
                    <ul class="nav nav-tabs border-tab" id="top-tab" role="tablist">
                        <li class="nav-item"><a class="nav-link active" id="vehicles-tab" data-bs-toggle="tab" href="#vehicles" role="tab" aria-controls="vehicles" aria-selected="true"><i class="icofont icofont-user-alt-3"></i>Bills</a></li>
                        <li class="nav-item"><a class="nav-link" id="contact-top-tab" data-bs-toggle="tab" href="#policies" role="tab" aria-controls="policies" aria-selected="false"><i class="icofont icofont-shield"></i>Payments</a></li>
                    </ul>
                    <div class="tab-content" id="top-tabContent">
                        <div class="tab-pane fade active show" id="vehicles" role="tabpanel" aria-labelledby="vehicles-tab"> 
                      
                       
                    <div class="table-responsive">
                     
                       @if(count($HotelInvoice)>0)
                       <table class="table table-xs">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Invoice No</th>
            <th scope="col">Type</th>
            <th scope="col">Customer</th>
            <th scope="col">Invoice Date</th>
            <th scope="col">Total Days</th>
            <th scope="col">Total Amount</th>
            <th scope="col">Status</th>
            <th scope="col">Created By</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($HotelInvoice as $invoice)
            @php
                $totalQty = 0;
                $totalAmount = 0;

                foreach ($invoice['hotel_invoice_items'] as $item) {
                    $days = \Carbon\Carbon::parse($item['end_date'])->diffInDays(\Carbon\Carbon::parse($item['start_date']));
                    $totalQty += $days;
                    $totalAmount += $days * $item['price'];
                }
            @endphp
            <tr>
                <th scope="row">{{ $loop->iteration }}.</th>
                <td><a href="#"><small>HF000{{ $invoice['id'] }}/025</small></a></td>
                <td>
                    @if($invoice['status'] == 'Pending')
                        PROFORMA INVOICE
                    @elseif($invoice['status'] == 'Confirmed')
                        INVOICE
                    @else
                        INVOICE
                    @endif
                </td>
                <td><a href="#"><small>{{ $invoice['hotel_customer']['name'] ?? 'N/A' }}</small></a></td>
                <td>{{ \Carbon\Carbon::parse($invoice['invoice_date'])->format('d/m/Y H:i:s') }}</td>
                <td>{{ number_format($totalQty, 2) }}</td>
                <td>{{ number_format($totalAmount, 2) }}</td>
                <td>{{ $invoice['status'] }}</td>
                <td>{{ $invoice['user']['first_name'] ?? 'N/A' }}</td>
                <td>
                    <a href="{{ route('hotel-invoice-download', ['id' => $invoice['id']]) }}" class="btn btn-outline-primary btn-xs">Print <i class="icofont icofont-printer"></i></a>
                    <a href="{{ route('hotel-invoice-preview', ['id' => $invoice['id']]) }}" class="btn btn-outline-primary btn-xs">View <i class="icofont icofont-eye"></i></a>

                    @if($invoice['status'] == 'Pending')
                        <a href="{{ route('hotel-invoice-status-update', ['id' => $invoice['id']]) }}" class="btn btn-outline-primary btn-xs">Confirm <i class="icofont icofont-tick-mark"></i></a>
                    @elseif($invoice['status'] == 'Confirmed')
                        <a href="{{ route('hotel-invoice-status-paid', ['id' => $invoice['id']]) }}" class="btn btn-outline-secondary btn-xs">Mark Paid <i class="icofont icofont-money"></i></a>
                    @else
                        <a href="#" class="btn btn-outline-info btn-xs">Paid <i class="icofont icofont-tick-mark"></i></a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

                        <br />
                      

                        @else 
                        <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                            <i class="icon-info-alt txt-danger"></i>
								No Records Found yet
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" ></button>
                       	</div>
                        @endif
					</div>

                        </div>
                        <div class="tab-pane fade" id="policies" role="tabpanel" aria-labelledby="policies-tab"> 
                        
                        <div class="table-responsive">
                       
					</div>
                        </div>
                      </div>
                  </div>
              </div>
          </div>
          @else
          <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                    <i class="icon-info-alt txt-danger"></i>
                       Employee Does not exit
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
          @endif
      </div>
  </div>



  @push('scripts')
	<script src="{{ asset('assets/js/datepicker/date-picker/datepicker.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.en.js') }}"></script>
    <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.custom.js') }}"></script>




	@endpush

  
@endsection

<!-- NEW QUOTATION MODAL START -->
<div class="modal fade" id="newModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Prepare Bill For <b>{{$Customers->name}}</b> </h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
            <div style="padding-right: 2em;padding-left: 2em;">
                        @livewire('hotel—management.customer-bill-details',['Customers_details'=>$Customers]) 
                    </div>
            </div>
        </div>
    </div>
    </div>
 <!-- NEW QUOTATION MODAL END -->
 
