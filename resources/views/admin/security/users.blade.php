@extends('layouts.admin.master')
@section('title')
{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}
@endsection
@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/date-picker.css') }}">
<style>
    .user-avatar {
        width: 38px;
        height: 38px;
        min-width: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 600;
        font-size: 14px;
    }
    .user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .user-cell .user-name {
        font-weight: 600;
    }
    .user-cell .user-email {
        display: block;
        font-size: 12px;
        color: #898989;
    }
    #usersTable .badge {
        font-weight: 500;
        letter-spacing: .2px;
    }
</style>
@endpush

@php
    $roleStyles = [
        'SUPER_ADMIN' => ['bg' => '#2e3d49', 'badge' => 'bg-dark'],
        'ADMIN'       => ['bg' => '#3989c6', 'badge' => 'bg-primary'],
        'Mbao'        => ['bg' => '#2f9e44', 'badge' => 'bg-success'],
        'Hardware'    => ['bg' => '#e8a13d', 'badge' => 'bg-warning text-dark'],
        'Driver'      => ['bg' => '#17a2b8', 'badge' => 'bg-info text-dark'],
    ];
    $defaultRoleStyle = ['bg' => '#8a8a8a', 'badge' => 'bg-secondary'];
@endphp

@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>{{ucfirst(str_replace('-',' ',Route::currentRouteName()))}}</h3>
    @endslot

    @slot('breadcrumb_action_buttons')
    <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#searchModal">Search <i class="icofont icofont-search-alt-1"></i></button></li>
      @if(Auth::user()->hasFullAccess())
        <li><button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newModal">New User <i class="icofont icofont-plus-circle"></i></button></li>
    @endif

    @endslot

    <li class="breadcrumb-item">{{ucfirst(explode('-', Route::currentRouteName())[0])}}</li>
    <li class="breadcrumb-item active">{{ucfirst(explode('-', Route::currentRouteName())[1])}}</li>
  @endcomponent

  <div class="container-fluid">
      <div class="row">
          <div class="col-sm-12">
              <div class="card">
                  <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                      <h5 class="mb-0"><i class="icofont icofont-people"></i> System Users</h5>
                      <span class="badge bg-light text-dark border">{{ $users->total() }} total</span>
                  </div>

                  <div class="card-body">
                  @foreach ($errors->all() as $error)
                  <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                    <i class="icon-info-alt txt-danger"></i>
                        {{ $error }}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                  @endforeach

                  @if($message = Session::get('error'))
                    <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                    <i class="icon-info-alt txt-danger"></i>
                        {!! $message !!}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close" data-bs-original-title="" title=""></button>
                    </div>
                    <br />
                    @endif

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
                        @if(count($users)>0)
						<table class="table table-hover align-middle" id="usersTable">
							<thead>
								<tr>
									<th scope="col">User</th>
                                    <th scope="col">Mobile</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Office</th>
                                    <th scope="col" class="text-end">Action</th>
								</tr>
							</thead>
							<tbody>
                                @foreach($users as $user)
                                    @php
                                        $style = $roleStyles[$user->role] ?? $defaultRoleStyle;
                                        $initials = strtoupper(substr($user->first_name, 0, 1));
                                        $canManage = Auth::user()->canManage($user);
                                    @endphp
								<tr>
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-avatar" style="background-color: {{ $style['bg'] }};">{{ $initials }}</div>
                                            <div>
                                                <span class="user-name">{{ strtoupper($user->first_name) }}</span>
                                                <span class="user-email">{{ $user->email }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>+255{{$user->mobile}}</td>
                                    <td><span class="badge {{ $style['badge'] }}">{{ $user->role }}</span></td>
                                    <td>
                                        @if($user->status == 'Active')
                                            <span class="badge bg-success"><i class="icofont icofont-check-circled"></i> Active</span>
                                        @else
                                            <span class="badge bg-danger"><i class="icofont icofont-close-circled"></i> Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{$user->office_location}} | {{ \App\Http\Controllers\Stock\ProductsController::storeName($user->office_location) }}</small>
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-xs dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="icofont icofont-settings-alt"></i> Actions
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#approvalModal{{$user->id}}">
                                                        <i class="fa fa-tags"></i> Assign Store
                                                    </a>
                                                </li>
                                                @if($canManage)
                                                <li>
                                                    <a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#roleModal{{$user->id}}">
                                                        <i class="fa fa-user-cog"></i> Change Role
                                                    </a>
                                                </li>
                                                @endif
                                                @if($canManage)
                                                <li><hr class="dropdown-divider"></li>
                                                @if($user->status == 'Active')
                                                <li>
                                                    <a class="dropdown-item text-danger" href="{!! Route('portal-user-status-update', ['id' => $user->id, 'status' => 'Inactive']) !!}">
                                                        <i class="icofont icofont-ui-delete"></i> Deactivate
                                                    </a>
                                                </li>
                                                @else
                                                <li>
                                                    <a class="dropdown-item text-success" href="{!! Route('portal-user-status-update', ['id' => $user->id, 'status' => 'Active']) !!}">
                                                        <i class="icofont icofont-check-circled"></i> Activate
                                                    </a>
                                                </li>
                                                @endif
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                   	</tr>
                                @endforeach
							</tbody>
						</table>
                        <br />
                        {{ $users->links() }}

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
                <h5 class="modal-title"><i class="icofont icofont-ui-user"></i> User Registration</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ Route('portal-users-add') }}">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="col-form-label">Full Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="icofont icofont-user"></i></span>
                                    <input class="form-control" type="text" value="{{ old('first_name') }}" required name="first_name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="icofont icofont-tag"></i></span>
                                    <input class="form-control" type="text" value="{{ old('first_name') }}" required name="username">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">Role</label>
                                <select class="form-select" required value="{{ old('role') }}" name="role">
                                    <option value="">--- Choose User Role ---</option>
                                    @foreach(Auth::user()->assignableRoles() as $assignableRole)
                                        <option value="{{ $assignableRole }}">{{ $assignableRole }}</option>
                                    @endforeach
								</select>
                            </div>
                        </div>


                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="col-form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="icofont icofont-envelope"></i></span>
                                    <input class="form-control" type="text" value="{{ old('email') }}" required name="email">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-form-label">Choose Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="icofont icofont-lock"></i></span>
                                    <input class="form-control" type="password" minlength="8" value="{{ old('password') }}" required name="password">
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-4">
                        <div class="form-group">
                                <label class="col-form-label">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="icofont icofont-phone"></i></span>
                                    <input class="form-control" type="text" minlength="9" maxlength="9" value="{{ old('mobile') }}" required name="mobile">
                                </div>
                        </div>

                        <div class="form-group">
                                <label class="col-form-label">Retype Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="icofont icofont-lock"></i></span>
                                    <input class="form-control" type="password" minlength="8" value="{{ old('password') }}" required name="password">
                                </div>
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
 <!-- NEW MODAL END -->

   <!-- SEARCH MODAL START -->
   <div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header  bg-primary text-white">
                <h5 class="modal-title"><i class="icofont icofont-search-alt-1"></i> User Search</h5>
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
                                <div class="input-group">
                                    <span class="input-group-text"><i class="icofont icofont-search-alt-1"></i></span>
                                    <input class="form-control" type="text" value="{{ old('reference_number') }}" maxlength="20" required id="reference_number">
                                </div>
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
  @endpush
@endsection
@foreach($users as $user)
<div class="modal fade" id="approvalModal{{$user->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa fa-tags"></i> Assign Store for {{ strtoupper($user->first_name) }}</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                <form method="post" action="{{ Route('edit-user-store') }}">
                    @csrf
                <div class="form-group">
                            <label class="col-form-label" >Choose Store</label>
                                <select class="form-select" required name="store_id">
                                <option value="">--- Choose Store ---</option>
                                        @foreach ($Store as $s)
                                    <option value='{{strtoupper($s->id)}}'>{{strtoupper($s->name)}}</option>
                                    @endforeach
									</select>
                                {{-- <i>Previous Store {{ $user->first_name }}</i> --}}
                                <input type="hidden" name="user_id"value="{{$user->id}}">

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
<div class="modal fade" id="roleModal{{$user->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa fa-user-cog"></i> Change Role for {{ strtoupper($user->first_name) }}</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('portal-users-update-role', ['id' => $user->id]) }}">
                    @csrf
                    <div class="form-group">
                        <label class="col-form-label">Role</label>
                        <select class="form-select" required name="role">
                            <option value="">--- Choose User Role ---</option>
                            @foreach(Auth::user()->assignableRoles() as $assignableRole)
                                <option value="{{ $assignableRole }}" @selected($user->role === $assignableRole)>{{ $assignableRole }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary" type="submit">Confirm & Update Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
