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
      
        <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">New <i class="icofont icofont-plus-circle"></i></button></li>
  

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
                        @if(count($OurTruck)>0)
						<table class="table table-xs table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Truck Plate No</th>
                                    <th>Driver</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($OurTruck as $index => $truck)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $truck->plate_no }}</td>
                                    <td>{{ $truck->driver->first_name ?? 'Unassigned' }}</td>
    <td>{{ $truck->creator->first_name ?? 'Unassigned' }}</td>
                                    <td>{{ $truck->created_at ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <br />
                        {{ $OurTruck->links() }}

                        @else 
                        <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                            <i class="icon-info-alt txt-danger"></i>
								No Records Found yet
                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" ></button>
                       	</div>
                        @endif
					</div>
                      </p>
                  </div>
              </div>
          </div>
      </div>
  </div>

 <!-- NEW TRUCK MODAL START -->
<div class="modal fade" id="newModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Truck Registration</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('add-truck') }}">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="col-form-label">Truck Plate Number</label>
                                <input class="form-control" type="text" required name="plate_no" placeholder="Enter Truck Plate No...">
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="col-form-label">Assign Driver</label>
                                <select class="form-control" required name="driver_id">
                                    <option value="">--- Choose Driver ---</option>
                                    @foreach ($User as $driver)
                                        <option value="{{ $driver->id }}">{{ strtoupper($driver->first_name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" type="submit">Register Truck</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- NEW TRUCK MODAL END -->


  @push('scripts')
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.js') }}"></script>
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.en.js') }}"></script>
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.custom.js') }}"></script>
  <script>
   
   
 </script>
  @endpush
@endsection



