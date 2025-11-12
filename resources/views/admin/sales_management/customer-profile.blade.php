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
                        <li class="nav-item"><a class="nav-link active" id="vehicles-tab" data-bs-toggle="tab" href="#vehicles" role="tab" aria-controls="vehicles" aria-selected="true"><i class="icofont icofont-user-alt-3"></i>Invoices</a></li>
                        <li class="nav-item"><a class="nav-link" id="contact-top-tab" data-bs-toggle="tab" href="#policies" role="tab" aria-controls="policies" aria-selected="false"><i class="icofont icofont-shield"></i>Payments</a></li>
                    </ul>
                    <div class="tab-content" id="top-tabContent">
                        <div class="tab-pane fade active show" id="vehicles" role="tabpanel" aria-labelledby="vehicles-tab"> 
                      
                       
                        <div class="table-responsive">
                        @if(count($Invoice)>0)
						<table class="table table-xs">
							<thead>
								<tr>
									<th scope="col">#</th>
									<th scope="col">invoice no</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Invoice date</th>
                                    <th scope="col">Total Quantity</th>

                                    <th scope="col">Total Amount</th>
                                    <th scope="col">Total Paid</th>
                                    <th scope="col">Total Remained</th>

                                    <th scope="col">Status</th>
                                    <th scope="col">Created By</th>
                                    <th scope="col">Action</th>
								</tr>
							</thead>
							<tbody>
                                @foreach($Invoice as $user)
                                <?php
                                    $totalQty = 0;
                                    $totalAmount = 0;

                                    foreach ($user['invoice_items'] as $item) {
                                        $totalQty += $item['qty'];
                                        $totalAmount += $item['qty'] * $item['price'];
                                    }
                                ?>
								<tr>
                              		<th scope="row">{{$loop->index + 1}}.</th>
                                    <td><a href="#"><small>SF000{{ $user->id }}/025 </small></a></td>
                                    <td>
                                    @if($user->status == 'Pending')
                                     PROFOMAL INVOICE
                                    @elseif($user->status == 'Confirmed')
                                    INVOICE
                                    @else
                                    INVOICE
                                    @endif
                                    </td>
                                    
                                    <td><a href=""><small>{{$user->Customer->name}}</small></a></td>
                                    <td>{{ \Carbon\Carbon::parse($user->invoice_date)->format('d/m/Y H:i:s') }}</td> <!-- Invoice Date -->
                                    <td>{{number_format($totalQty, 2)}}</td>
                                    <td>{{number_format($user->total_invoice_amount, 2)}}</td>
                                    <td>{{number_format($user->amount_paid, 2)}}</td>
                                    <td>{{number_format($user->amount_remained, 2)}}</td>

                                    <td>{{$user->status}}</td>
                                    <td>{{$user->User->first_name}}</td>
                                    <td>

                                    <a href="{{ Route('invoice-download', ['id' => $user->id]) }}" class="btn btn-outline-primary btn-xs" style="margin-top: auto;" type="button">Print <i class="icofont icofont-printer"></i></a>
                                    <a href="{{ Route('invoice-preview', ['id' => $user->id]) }}" class="btn btn-outline-primary btn-xs" style="margin-top: auto;" type="button">View <i class="icofont icofont-eye"></i></a>
                                    @if($user->status == 'Pending')
                                    <a href="{{ Route('invoice-status-update', ['id' => $user->id]) }}" class="btn btn-outline-primary btn-xs" style="margin-top: auto;" type="button">Confirm <i class="icofont icofont-tick-mark"></i></a>
                                   
                                    @elseif($user->status == 'Confirmed' || $user->status == 'Partial_Paid' )
                                    {{-- <a href="{{ Route('invoice-status-paid', ['id' => $user->id]) }}" class="btn btn-outline-secondary btn-xs" style="margin-top: auto;" type="button">Mark Paid <i class="icofont icofont-money"></i></a> --}}
                                    <a  class="btn btn-primary btn-xs" data-bs-toggle="modal" data-bs-target="#approvalModal{{$user->id}}"><i class='fa fa-tags'></i> Receive Payment</a>
                                    <a  class="btn btn-info btn-xs" data-bs-toggle="modal" data-bs-target="#approvalModal_payment{{$user->id}}"><i class='fa fa-tags'></i> Payment Details</a>
                                    @else
                                    {{-- <a href="#" class="btn btn-outline-info btn-xs" style="margin-top: auto;" type="button">Payment Details <i class="icofont icofont-tick-mark"></i></a> --}}
                                    <a  class="btn btn-info btn-xs" data-bs-toggle="modal" data-bs-target="#approvalModal_payment{{$user->id}}"><i class='fa fa-tags'></i> Payment Details</a>
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
                        @if(count($sales)>0)
						<table class="table table-xs">
							<thead>
								<tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Barcode</th>
                                <th>Qnty</th>
                                <th>Price</th>
                                <th>Sub Total</th>
								</tr>
							</thead>
							<tbody>

                                @foreach($sales as $user)
								<tr>
                                <th scope="row">{{$loop->index + 1}}.</th>
                                    <td>{{ $user->Product->product_name}}</td>
                                    <td>{{$user->Product->barcode}}</td> 
                                    <td>{{$user->qty }}</td>
                                    <td>{{ number_format($user->price, 2) }} TZS</td> 
                                    <td>{{ number_format($user->price*$user->qty, 2) }} TZS</td> 
                                    <td>
                                </td> 
                                    
							</td>
                                   	</tr>
                                @endforeach
							</tbody>
						</table>
                        <br />
                        {{ $sales->links() }}

                        @else 
                        <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                            <i class="icon-info-alt txt-danger"></i>
								No Records Found yet
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" ></button>
                       	</div>
                        @endif
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
  <script>
    Livewire.on('InvoiceUpdated', () => {
        // Optional: Close the modal
        $('#newModal').modal('hide');

        // Navigate back
        setTimeout(() => {
            window.history.back();
        }, 500); // Slight delay ensures smooth transition
    });
</script>
  @endpush
@endsection

<!-- NEW QUOTATION MODAL START -->
<div class="modal fade" id="newModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
        <div class="modal-header bg-primary text-white">
    <h5 class="modal-title">
        Operate Sales For <b>{{ $Customers->name }}</b>
    </h5>

    <!-- Refresh Button -->
    <button class="btn btn-light btn-sm me-2" onclick="window.location.reload();">
        Refresh Page
    </button>

    <!-- Close Button -->
    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
            <div class="modal-body">
            <div style="padding-right: 2em;padding-left: 2em;">
                        @livewire('sales-management.oparate-sales',['Customers_details'=>$Customers]) 
                    </div>
            </div>
        </div>
    </div>
    </div>
 <!-- NEW QUOTATION MODAL END -->
 <!-- NEW MODAL END -->
 @foreach($Invoice as $user)
<div class="modal fade" id="approvalModal_payment{{$user->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Payment Details for Invoice #{{ $user->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                @if($user->invoice_payment_details->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Amount Submitted</th>
                                    <th>Date Paid</th>
                                    <th>Status</th>
                                    <th>Payer Name</th>
                                    <th>Receipt</th>
                                    <th>Channel</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->invoice_payment_details as $index => $payment)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ number_format($payment->amount_submitted, 2) }} TZS</td>
                                        <td>{{ \Carbon\Carbon::parse($payment->date_payed)->format('d-m-Y H:i') }}</td>
                                        <td>
                                            <span class="badge {{ $payment->status == 'Paid' ? 'bg-success' : 'bg-warning' }}">
                                                {{ $payment->status }}
                                            </span>
                                        </td>
                                        <td>{{ $payment->payer_id }}</td>
                                        <td>{{ $payment->receipt_number }}</td>
                                        <td>{{ $payment->channel }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        No payments recorded for this invoice yet.
                    </div>
                @endif
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach


@foreach($Invoice as $user)
<!-- Payment Modal -->
<div class="modal fade" id="approvalModal{{$user->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            
            <!-- Modal Header -->
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Enter Amount Received</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body">
                <form method="post" action="{{ route('receive-invoice-payment') }}" id="paymentForm_{{$user->id}}">
                    @csrf
                    <div class="mb-3">
                        <label for="discount_amount_{{$user->id}}" class="form-label">Enter New Amount</label>
                        <input 
                            type="number" 
                            class="form-control" 
                            id="discount_amount_{{$user->id}}" 
                            name="discount_amount" 
                            required 
                            min="0" 
                            max="{{ $user->amount_remained }}" 
                            step="0.01" 
                            placeholder="Enter amount (max {{ number_format($user->amount_remained, 2) }})"
                        >
                    </div>

                    <div class="mb-3">
                        <strong>Total Invoiced Amount:</strong> {{ number_format($user->total_invoice_amount, 2) }} TZS<br>
                        <strong>Total Paid Amount:</strong> {{ number_format($user->amount_paid, 2) }} TZS<br>
                        <strong>Amount Remaining will be:</strong> 
                        <span id="remaining_amount_{{$user->id}}" class="text-danger">{{ number_format($user->amount_remained, 2) }} TZS</span>
                    </div>
                    
                    <input type="hidden" name="item_id" value="{{ $user->id }}">
                    
                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="confirmPayment({{$user->id}})">Confirm & Apply</button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>
@endforeach

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">Confirm Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="confirmModalBody">
                <!-- Will be updated dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, edit</button>
                <button type="button" class="btn btn-success" id="confirmYesBtn">Yes, submit</button>
            </div>
        </div>
    </div>
</div>


<!-- Toast Container (Top Right) -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1080">
    <div id="exceedToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                Entered amount exceeds remaining amount!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- JS: Validation + Confirmation + Duplicate Click Prevention -->
<script>
let formToSubmit = null;
let isSubmitting = false;

// Function to handle "Confirm & Apply" button
function confirmPayment(userId) {
    if (isSubmitting) return; // prevent double click

    const input = document.getElementById('discount_amount_' + userId);
    const maxAmount = parseFloat(input.max);

    if (!input.value || parseFloat(input.value) <= 0) {
        alert("Please enter a valid amount.");
        return;
    }

    if (parseFloat(input.value) > maxAmount) {
        // Show toast warning
        const toastEl = document.getElementById('exceedToast');
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();

        // Reset input to max allowed
        input.value = maxAmount;
        return;
    }

    // Update confirmation modal text with the entered amount
    const amountEntered = parseFloat(input.value).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
    const modalBody = document.getElementById('confirmModalBody');
    modalBody.textContent = `You are about to submit this payment with amount ${amountEntered} TZS. Do you want to proceed?`;

    // Store form reference and show confirmation modal
    formToSubmit = document.getElementById('paymentForm_' + userId);
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    confirmModal.show();
}

// Triggered when user clicks "Yes, submit"
document.getElementById('confirmYesBtn').addEventListener('click', function() {
    if (formToSubmit && !isSubmitting) {
        isSubmitting = true; // prevent double submission
        formToSubmit.submit();
    }
});

// Real-time remaining amount update & toast if exceeded
@foreach($Invoice as $user)
const input{{$user->id}} = document.getElementById('discount_amount_{{$user->id}}');
const remainingDisplay{{$user->id}} = document.getElementById('remaining_amount_{{$user->id}}');
const maxAmount{{$user->id}} = parseFloat(input{{$user->id}}.max);

input{{$user->id}}.addEventListener('input', function() {
    let value = parseFloat(this.value);
    if (isNaN(value) || value < 0) value = 0;

    if (value > maxAmount{{$user->id}}) {
        const toastEl = document.getElementById('exceedToast');
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
        this.value = maxAmount{{$user->id}};
        value = maxAmount{{$user->id}};
    }

    const newRemaining = maxAmount{{$user->id}} - value;
    remainingDisplay{{$user->id}}.textContent = newRemaining.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' TZS';
});
@endforeach
</script>




 

  <!-- NEW MODAL START -->
  <div class="modal fade" id="newModalDetils" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Invoice Items</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
            <table class="table table-xs">
    <thead>
        <tr>
        <th>#</th>
            <th>Product Name</th>
            <th>Barcode</th>
            <th>Qnty</th>
            <th>Price</th>
            <th>Sub Total</th>
        </tr>
    </thead>
    <tbody>
    @foreach($Invoice as $user)
    @foreach ($user->invoice_items as $item)
            <tr>
            <th scope="row">{{$loop->index + 1}}.</th>
                <td>{{ $item->Product->product_name}}</td>
                <td>{{$item->Product->barcode}}</td> 
                <td>{{$item->qty }}</td>
                <td>{{ number_format($item->price, 2) }} TZS</td> 
                <td>{{ number_format($item->price*$item->qty, 2) }} TZS</td> 
                <td>
    		  </td> 
            </tr>
    @endforeach
    @endforeach
    </tbody>
</table> 
                </div>
        
       
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
               
                </form>
            </div>
        </div>
    </div>
    </div>

    @push('scripts')
	<script src="{{asset('assets/js/sweet-alert/sweetalert.min.js')}}"></script>
	<script src="{{asset('assets/js/sweet-alert/sweetalert.min.js')}}"></script>
	<script>
	
	  window.addEventListener('swal:modal', event => { 
		  swal({
			title: event.detail.message,
			text: event.detail.text,
			icon: event.detail.type,
			buttons:false,
			customClass:'swal-wide'
		  });
	  });
		
	  window.addEventListener('swal:confirm', event => { 
		  swal({
			title: event.detail.message,
			text: event.detail.text,
			icon: event.detail.type,
			buttons: true,
			dangerMode: true,
		  })
		  .then((willDelete) => {
			if (willDelete) {
			  window.livewire.emit('remove');
			}
		  });
	  });
	   </script>
       <script>
    window.addEventListener('show-print-button', event => {
        const btn = document.createElement('a');
        btn.href = event.detail.url;
        btn.className = 'btn btn-outline-primary btn-xs';
        btn.style = 'margin-top: 10px;';
        btn.target = '_blank';
        btn.innerHTML = 'Print Invoice <i class="icofont icofont-printer"></i>';

        // Append the button somewhere appropriate
        const alertContainer = document.querySelector('.swal-modal');
        if (alertContainer) {
            const div = document.createElement('div');
            div.style.marginTop = '15px';
            div.appendChild(btn);
            alertContainer.appendChild(div);
        }
    });
</script>

    @endpush
  