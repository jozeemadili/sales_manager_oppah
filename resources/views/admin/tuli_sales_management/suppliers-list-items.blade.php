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
    <!-- <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#searchModal">Search <i class="icofont icofont-search-alt-1"></i></button></li> -->
      @if(Auth::user()->role == 'ADMIN')
        {{-- <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">New <i class="icofont icofont-plus-circle"></i></button></li> --}}
    @endif

    @endslot
    
    <li class="breadcrumb-item">{{ucfirst(explode('-', Route::currentRouteName())[0])}}</li>
    <li class="breadcrumb-item active">{{ucfirst(explode('-', Route::currentRouteName())[1])}}</li>
  @endcomponent
  
  <div class="container-fluid">
      <div class="row">
          <div class="col-sm-12">
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
                        @if(count($Branch)>0)
						<table class="table table-xs">
							<thead>
								<tr>
									<th scope="col">#</th>
									<th scope="col">Supplier Name</th>
                                    <th scope="col">Vihecle No</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Invetory Date</th>
                                
                                    <th scope="col">Status</th>
                                    <th scope="col">Created By</th>
                                    <th scope="col">Action</th>
								</tr>
							</thead>

							<tbody>
                                @foreach($Branch as $user)
								<tr>
									<th scope="row">{{$loop->index + 1}}.</th>
									<td><a href=""><small>{{strtoupper($user->company_name)}}</small></a></td>
                                    <td><a href=""><small>{{strtoupper($user->vihecle_no)}}</small></a></td>
                                    <td><a href=""><small>{{$user->description}}</small></a></td>
                                    <td><a href=""><small>{{$user->inventory_date}}</small></a></td>
                                    <td>{{$user->status}}</td>
                                    <td>{{$user->user->first_name}}</td>
                                    <td>
                                    <div class="pull-right"> <a href='{!! Route('inventory-preview', ['id' => $user->id]) !!}' class='btn btn-outline-info btn-xs'> view </a>
                                    {{-- @if($user->status == 'Active')
                                    <div class="pull-right"> <a href='{!! Route('branch-status-update', ['id' => $user->id, 'status' => 'Inactive']) !!}' class='btn btn-outline-danger btn-xs'>Deactivate <i class="icofont icofont-ui-delete"></i></a></div>  
                                    @else
                                    <div class="pull-right"> <a href='{!! Route('branch-status-update', ['id' => $user->id, 'status' => 'Active']) !!}' class='btn btn-outline-info btn-xs'>Activate <i class="icofont icofont-ui-delete"></i></a></div>  
                                    @endif --}}
                                    
							</td>
                                   	</tr>
                                @endforeach
							</tbody>
						</table>
                        <br />
                        {{ $Branch->links() }}

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



  @push('scripts')
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.js') }}"></script>
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.en.js') }}"></script>
  <script src="{{ asset('assets/js/datepicker/date-picker/datepicker.custom.js') }}"></script>
  <script>
    document.getElementById("reference_number").placeholder = "Enter user's ID Number ...";
    document.getElementById('reference_number').name = 'id_number';
    function searchBy(search_by)
    {
        if(search_by.value == "id_number")
        {
            document.getElementById("reference_number").type = "text";
            document.getElementById("reference_number").placeholder = "Enter user's ID Number ...";
            document.getElementById('reference_number').name = 'id_number';
        }
        else if(search_by.value == "phone_number")
        {
            document.getElementById("reference_number").maxlength = "9";
            document.getElementById("reference_number").type = "number";
            document.getElementById("reference_number").placeholder = "Enter user's Phone (e.g. 766192332) ...";
            document.getElementById('reference_number').name = 'phone_number';
            
        }
        else
        {
            document.getElementById("reference_number").type = "text";
            document.getElementById("reference_number").placeholder = "Enter user's First Name or Middle Name or Last Name ...";
            document.getElementById('reference_number').name = 'cname';
        }
    }
 </script>
 
  @endpush
@endsection
