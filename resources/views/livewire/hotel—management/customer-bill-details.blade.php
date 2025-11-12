<div>
<div class="row">
<div class="col-lg-4">
<label>Choose Room And Dates Used By <b>{{$Customers_details->name}}</b> </label>
<div class="row">
<div class="form-group">
    <label for="startDate">Select Start Date</label>
    <input wire:model="startDate" type="date" id="startDate" class="form-control">
</div>
<div class="form-group">
    <label for="startDate">End Date</label>
    <input wire:model="endDate" type="date" id="endDate" class="form-control">
</div>



</div>
<br/>

<div class="form-group">
    <label>Choose Room</label>
    <input class="form-control" wire:model="customerquery" type="text" placeholder="Search by Product Name or Barcode" aria-label="Search by Product Name or Barcode">

    <div class="list-group mt-2">
        @foreach ($customers as $customer)
        <a 
    @if($customer->occupied === 'no') 
        wire:click="selectCustomer({{ $customer }})" 
    @endif 
    class="list-group-item list-group-item-action" href="javascript:void(0)">
    
    <div class="d-flex w-100 justify-content-between align-items-center">
        <small>
            <b>{{ strtoupper($customer->room_name) }} | {{ strtoupper($customer->rooms_category->category_name) }}</b>
        </small>
        <small class="text-muted">
            <font color='{{ $customer->occupied === "no" ? "green" : "red" }}'>
                <i>Occupied?</i>
            </font>
            <span class="badge badge-{{ $customer->occupied === "no" ? "primary" : "danger" }} rounded-pill counter">
                {{ $customer->occupied }}
            </span>
        </small>
    </div>

    <small class="text-muted d-block">
        {{ number_format($customer->rooms_category->price_day, 2) }} {{ strtoupper($customer->rooms_category->currency) }}  
        <i>{{ strtoupper($customer->rooms_category->ammenties) }}</i> 
    </small>

    @if($customer->occupied === 'no') 
        <small class="text-success d-block">
            <i>No Booking Details</i>
        </small>
    @else
        @php
            $latestBooking = collect($customer->room_bookings)
                ->sortByDesc('start_date')
                ->first();
        @endphp

        @if($latestBooking)
            <div class="bg-light border rounded p-2 mt-2">
                <small class="d-block text-dark">
                    <strong>From Date:</strong> {{ \Carbon\Carbon::parse($latestBooking['start_date'])->format('d M Y') }}
                </small>
                <small class="d-block text-dark">
                    <strong>To Date:</strong> {{ \Carbon\Carbon::parse($latestBooking['end_date'])->format('d M Y') }}
                </small>
                <small class="d-block text-dark">
                    <strong>Status:</strong> {{ ucfirst($latestBooking['status']) }}
                </small>
            </div>
        @endif
    @endif
</a>

        @endforeach
    </div>
@if (session()->has('error'))
    <div class="alert alert-danger mt-2">{{ session('error') }}</div>
@endif
</div>




  
    </div>
    <!-- ----------------- -->
    <div class="col-lg-8">
      
    
    @if(count($bookings)>0)
       <table class="table table">
    <thead>
        <tr>
            <th>Room Selected</th>
            <th>Price</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>No Of Days</th>
            <th>Sub Total</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total = 0;
        @endphp

        @foreach ($bookings as $booking)
            @php
                $startDate = \Carbon\Carbon::parse($booking->start_date);
                $endDate = \Carbon\Carbon::parse($booking->end_date);
                $noOfDays = $startDate->diffInDays($endDate);
                $subTotal = $noOfDays * $booking->room->rooms_category->price_day;
                $total += $subTotal;
            @endphp
            <tr>
                <td>{{ $booking->room->room_name }}</td>
                <td>{{ number_format($booking->room->rooms_category->price_day, 2) }} {{ $booking->room->rooms_category->currency }}</td>
                <td>{{ $startDate->format('Y-m-d') }}</td>
                <td>{{ $endDate->format('Y-m-d') }}</td>
                <td>{{ $noOfDays }}</td>
                <td>{{ number_format($subTotal, 2) }} {{ $booking->room->rooms_category->currency }}</td>
                <td>
                <td>
                    <a wire:click="addItem({{ $booking }})" class='btn btn-primary btn-xs btn-outline' href="javascript:void(0)" >+1 day</a>
                    <a wire:click="removeItem({{ $booking }})" class='btn btn-secondary btn-xs btn-outline' href="javascript:void(0)" >-1 day</a>
			         <a wire:click="deleteItem({{ $booking }})" class='btn btn-danger btn-outline btn-xs pull-right' href="javascript:void(0)" >x</a>
				  </td> 
                </td>
            </tr>
        @endforeach

        <tr>
            <th>Total</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>{{ number_format($total, 2) }} TZS</th>
        </tr>
    </tbody>
</table>
<div class="f1-buttons">
        <hr />
        <div class="pull-right">

         @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    <!-- <a href="{{ Route('invoice-download', ['id' => '2']) }}" class="btn btn-outline-primary btn-next" style="margin-top: auto;" type="button">Print <i class="icofont icofont-printer"></i></a> -->

        <button 
                 class="btn btn-outline-primary btn-next" 
                 wire:click="generateInvoice"
                style="margin-top: auto; display: ''" 
                type="button">
           Generate Bill
            <i class="icofont icofont-printer"></i> 
        </button>
        

        </div>
</div>
@else
        Search and select  to add
@endif

    </div>
    
</div>

</div>
