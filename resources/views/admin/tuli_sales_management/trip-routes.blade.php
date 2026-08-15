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
    <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#searchModal">Search <i class="icofont icofont-search-alt-1"></i></button></li>
      
        <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addTruckRouteModal">New <i class="icofont icofont-plus-circle"></i></button></li>
  

    @endslot
    
    <li class="breadcrumb-item">{{ucfirst(explode('-', Route::currentRouteName())[0])}}</li>
    <li class="breadcrumb-item active">{{ucfirst(explode('-', Route::currentRouteName())[1])}}</li>
  @endcomponent
  
  <div class="container-fluid">
      <div class="row">
          <div class="col-sm-12">
          @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

              <div class="card">

                  <div class="card-body">
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

                    <p>
                      <div class="table-responsive">
                        @if(count($TrucksRoute) > 0)
                        <table class="table table-xs table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Trip No</th>
                                    <th>Route Date</th>
                                    <th>Truck Plate No</th>
                                    <th>Driver</th>
                                    <th>Going Customer</th>
                                    <th>Return Customer</th>
                                    <th>Going Fee</th>
                                    <th>Return Fee</th>
                                    <th>Total Fee</th>
                                    <th>Status</th>
                                    
                                    <th>Created By</th>
                                    <th>Created Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                    
                            <tbody>
                                @foreach($TrucksRoute as $index => $route)
                                    <tr>
                                        <td>{{ $loop->iteration + ($TrucksRoute->currentPage() - 1) * $TrucksRoute->perPage() }}</td>
                                        <td><strong>{{ $route->trip_no }}</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($route->route_date)->format('d M Y') }}</td>
                                        <td>{{ $route->our_truck->plate_no ?? 'N/A' }}</td>
                                        <td>{{ $route->our_truck->driver->first_name ?? 'Unassigned' }}</td>
                                        <td>{{ $route->going_customer }}</td>
                                        <td>{{ $route->return_customer ?? '-' }}</td>
                                        <td>{{ number_format($route->going_transport_fee ?? 0, 2) }}</td>
                                        <td>{{ number_format($route->return_transport_fee ?? 0, 2) }}</td>
                                        <td>
                                            {{ number_format(
                                                $route->total_fee ??
                                                (($route->going_transport_fee ?? 0) + ($route->return_transport_fee ?? 0)), 2
                                            ) }}
                                        </td>
                                        <td>{{ $route->status }}</td>
                                        <td>{{ $route->user->first_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($route->created_date)->format('d M Y h:i A') }}</td>
                                        <td> 
                                            <div class="pull-left"> <a href='{!! Route('route-preview', ['id' => $route->id]) !!}' class='btn btn-outline-info btn-xs'> view </a>
                                                
                                                @if($route->status == 'Pending')
                                                <div class="pull-right">
                                                    <a href="{!! Route('delete-unsubmited-route', ['id' => $route->id, 'status' => 'Inactive']) !!}" 
                                                       class="btn btn-outline-danger btn-xs"
                                                       onclick="return confirm('Are you sure you want to delete all inventory data?')">
                                                       Delete <i class="icofont icofont-ui-delete"></i>
                                                    </a>
                                                  </div>
                                                @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    
                        {{-- Pagination --}}
                        <div class="d-flex justify-content-center mt-3">
                            {{ $TrucksRoute->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">No truck routes have been added yet.</div>
                    @endif
                    
					</div>
                      </p>
                  </div>
              </div>
          </div>
      </div>
  </div>

 <div class="modal fade" id="addTruckRouteModal" tabindex="-1" role="dialog" aria-labelledby="addTruckRouteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
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
                        <button type="submit" class="btn btn-primary">Save Route</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


 


  @push('scripts')
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.js') }}"></script>
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.en.js') }}"></script>
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.custom.js') }}"></script>
  <script>
   
   
 </script>
 <script>
    document.addEventListener('DOMContentLoaded', function() {
        // When modal opens, fetch trip number
        $('#addTruckRouteModal').on('show.bs.modal', function () {
            fetch('{{ route("generate-trip-no") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('trip_no').value = data.trip_no;
                })
                .catch(err => console.error('Error fetching trip no:', err));
        });
    });
    </script>
    
  @endpush
@endsection



