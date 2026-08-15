@extends('layouts.admin.master')
@section('title', 'Dashboard')

@push('css')

@endpush

@section('content')
<div class="container">

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
   
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Truck Route</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
    
                <div class="modal-body">
                    <form method="POST" action="{{ route('add-truck-route') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Route Date</label>
                                    <input type="date" class="form-control" name="route_date" required>
                                </div>
                            </div>
    
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Trip No</label>
                                    <input type="text" class="form-control" id="trip_no" name="trip_no" readonly>
                                </div>
                            </div>
                            
    
                            <div class="col-md-4">
                                @if(Auth::user()->role == 'ADMIN')
                                <div class="form-group">
                                    <label>Truck</label>
                                    <select class="form-control" name="truck_id" required>
                                        <option value="">--- Select Truck ---</option>
                                        @foreach ($OurTruck as $truck)
                                            <option value="{{ $truck->id }}">
                                                {{ $truck->plate_no }} | {{ $truck->driver?->first_name ?? 'Unassigned' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                    
                            @elseif(Auth::user()->role == 'Driver')
                                @php
                                    // Since controller limits trucks by driver, we just grab the first (or only) one
                                    $truck = $OurTruck->first();
                                @endphp
                    
                                <div class="form-group">
                                    <label>My Truck</label>
                                    @if($truck)
                                        {{-- Hidden input for truck_id (used when submitting form) --}}
                                        <input type="hidden" name="truck_id" value="{{ $truck->id }}">
                    
                                        {{-- Read-only display of truck info --}}
                                        <input type="text" class="form-control" 
                                               value="Plate no : {{ $truck->plate_no }}, Driver : {{ $truck->driver?->first_name ?? 'Unassigned' }}" 
                                               readonly>
                                    @else
                                        <input type="text" class="form-control" value="No truck assigned" readonly>
                                    @endif
                                </div>
                            @endif
                            
    
                            </div>
    
                        </div>
    
                        <hr>
    
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Going Customer</label>
                                    <input type="text" class="form-control" name="going_customer" required placeholder="Enter Going Customer...">
                                </div>
                            </div>
    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Return Customer</label>
                                    <input type="text" class="form-control" name="return_customer" placeholder="Enter Return Customer...">
                                </div>
                            </div>
                        </div>
    
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Going Transport Fee</label>
                                    <input type="number" step="0.01" class="form-control" name="going_transport_fee" placeholder="0.00">
                                </div>
                            </div>
    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Return Transport Fee</label>
                                    <input type="number" step="0.01" class="form-control" name="return_transport_fee" placeholder="0.00">
                                </div>
                            </div>
    
                            {{-- <div class="col-md-4">
                                <div class="form-group">
                                    <label>Total Fee</label>
                                    <input type="number" step="0.01" class="form-control" name="total_fee" placeholder="0.00">
                                </div>
                            </div> --}}
                        </div>
    
                        <input type="hidden" name="created_by" value="{{ Auth::user()->name }}">
                        <input type="hidden" name="created_date" value="{{ now() }}">
    
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary w-100">Save Route</button>

                        </div>
                    </form>
                </div>
          
    </div>
</div>
@endsection

@push('scripts')
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.js') }}"></script>
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.en.js') }}"></script>
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.custom.js') }}"></script>
  <script>
   
   
 </script>
 <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch trip number immediately when page loads
        fetch('{{ route("generate-trip-no") }}')
            .then(response => response.json())
            .then(data => {
                // Set trip number input value
                const tripInput = document.getElementById('trip_no');
                if (tripInput) {
                    tripInput.value = data.trip_no;
                }
            })
            .catch(err => console.error('Error fetching trip no:', err));
    });
    </script>
