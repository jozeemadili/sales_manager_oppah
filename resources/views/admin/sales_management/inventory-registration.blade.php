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
      @if(Auth::user()->hasFullAccess())
        <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">New <i class="icofont icofont-plus-circle"></i></button></li>
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
                                    <th scope="col">Store</th>
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
                                    <td>{{$user->store->name}}</td>
                                    <td>{{$user->status}}</td>
                                    
                                    <td>{{$user->user->first_name}}</td>
                                    <td>
                                    <div class="pull-left"> <a href='{!! Route('inventory-preview', ['id' => $user->id]) !!}' class='btn btn-outline-info btn-xs'> view </a>
                                    @if($user->status == 'Active')
                                    {{-- <div class="pull-right"> <a href='{!! Route('delete-unsubmited-invetory', ['id' => $user->id, 'status' => 'Inactive']) !!}' class='btn btn-outline-danger btn-xs'>Deactivate <i class="icofont icofont-ui-delete"></i></a></div>  --}}
                                    <div class="pull-right">
                                        <a href="{!! Route('delete-unsubmited-invetory', ['id' => $user->id, 'status' => 'Inactive']) !!}" 
                                           class="btn btn-outline-danger btn-xs"
                                           onclick="return confirm('Are you sure you want to delete all inventory data?')">
                                           Delete <i class="icofont icofont-ui-delete"></i>
                                        </a>
                                      </div>
    
                                    @else

                                    @endif
                                    
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

 <!-- NEW MODAL START -->
 <div class="modal fade" id="newModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Inventory Registration</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ Route('add-inventory') }}">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-form-label">Supplier / Company Name</label>
                            
                                <!-- Dropdown list -->
                                <select class="form-control" id="companySelect" name="company_name" onchange="handleCompanySelect(this)">
                                    <option value="">-- Select Company --</option>
                                    @foreach($companyNames as $name)
                                        <option value="{{ $name }}">{{ $name }}</option>
                                    @endforeach
                                    <option value="__manual__">Other (Add New)</option>
                                </select>
                            
                                <!-- Hidden input for manual entry -->
                                <input class="form-control mt-2 d-none" type="text" id="manualCompanyInput" name="manual_company_name" placeholder="Enter new company name">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">Select Store</label>
                                <select class="form-control"  name="store_id" >
                                    <option value="">-- Select Store --</option>
                                    @foreach($companystore as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" > Vihecle Number</label>
                                <input class="form-control" type="text" value="{{ old('branch_name') }}" required  name="vihecle_no">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" > Date Received</label>
                                <input class="form-control" type="date" value="{{ old('branch_name') }}" required  name="inventory_date">
                            </div>
                            <div class="form-group">
                                <label class="col-form-label" >Description</label>
                                <textarea class="form-control" type="date" value="{{ old('branch_name') }}" required  name="description">na</textarea>
                            </div>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Confirm & Register</button>
                </form>
            </div>
        </div>
    </div>
    </div>

   <!-- SEARCH MODAL START -->
   <div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-primary text-white">
                <h5 class="modal-title">User Search</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ url()->current() }}">
                    @csrf

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-form-label">Search By</label><br>
                                <input type="radio" checked class="radio_animated" value="id_number" name="search_by" id="search_by" onChange="searchBy(this)"> Identity No.
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" class="radio_animated" value="phone_number" name="search_by" id="search_by" onChange="searchBy(this)"> Phone No.
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" class="radio_animated" value="name" name="search_by" id="search_by" onChange="searchBy(this)"> Name
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <input class="form-control" type="text" value="{{ old('reference_number') }}" maxlength="20" required id="reference_number">
                            </div>
                        </div>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal" >Close</button>
                <button class="btn btn-primary" type="submit" >Search</button>
                </form>
            </div>
        </div>
    </div>
    </div>
 <!-- SEARCH MODAL END -->

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
 <script>
    function handleCompanySelect(selectElement) {
        const manualInput = document.getElementById('manualCompanyInput');
    
        if (selectElement.value === '__manual__') {
            manualInput.classList.remove('d-none');
            manualInput.setAttribute('name', 'company_name'); // make it the field that gets submitted
            selectElement.removeAttribute('name'); // avoid duplicate names
        } else {
            manualInput.classList.add('d-none');
            manualInput.removeAttribute('name');
            selectElement.setAttribute('name', 'company_name');
        }
    }
    </script>
    
  @endpush
@endsection
