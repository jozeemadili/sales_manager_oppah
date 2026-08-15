@extends('layouts.admin.master')

@section('title')
{{ ucfirst(str_replace('-', ' ', Route::currentRouteName())) }}
@endsection

@push('css')
<style>
    .info-card {
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 1rem;
        transition: 0.3s;
    }
    .info-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .info-title {
        font-weight: 600;
        color: #0d6efd;
    }
    .info-value {
        font-size: 1rem;
        color: #333;
    }
</style>
@endpush

@section('content')
@component('components.breadcrumb')
    @slot('breadcrumb_title')
        <h3>{{ ucfirst(str_replace('-', ' ', Route::currentRouteName())) }}</h3>
    @endslot
    
    
    @slot('breadcrumb_action_buttons')

    @if($TrucksRoute->status == 'Pending')
    <li><a href='{!! Route('send-approve', ['id' => $TrucksRoute->id]) !!}' class='btn btn-outline-primary'>Send To Stock</a> </li>

   
    <li>
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal_expense">
            Record Expense <i class="icofont icofont-plus-circle"></i>
        </button>
    </li>
    <li>
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal_route_plan">
            Route Plan <i class="icofont icofont-plus-circle"></i>
        </button>
    </li>
    @else
    <li><a href='#' class='btn btn-outline-danger'>Request submitted —  cannot be Edited</a> </li>
    <li><a href="https://wa.me/?text={{ urlencode(route('free-ledger-download', $TrucksRoute->id)) }}" target="_blank" class="btn btn-info">
        Share via WhatsApp
    </a></li>
    <li><a href="{{ route('invoice-download-ledger', ['id' => $TrucksRoute->id]) }}" class="btn btn-outline-primary ">Print</a></li>
    @endif

        
    @endslot

    <li class="breadcrumb-item">{{ ucfirst(explode('-', Route::currentRouteName())[0]) }}</li>
    <li class="breadcrumb-item active">{{ ucfirst(explode('-', Route::currentRouteName())[1]) }}</li>
@endcomponent

<div class="container-fluid">

    {{-- ALERTS --}}
    @foreach ($errors->all() as $error)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="icon-info-alt txt-danger"></i> {{ $error }}
            <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
        </div>
    @endforeach

    @if($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="icofont icofont-check-circled"></i> {!! $message !!}
            <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- MAIN CARD: DETAILS + SUMMARY --}}
    <div class="card shadow-lg border-0 mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="icofont icofont-truck"></i> Truck Route Details & Summary</h5>
            <span class="badge bg-light text-primary">#{{ $TrucksRoute->id }}</span>
        </div>

        <div class="card-body">
            {{-- BASIC INFO --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="info-card p-3 bg-light">
                        <p class="info-title mb-1">Trip Number</p>
                        <p class="info-value mb-0">{{ $TrucksRoute->trip_no }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card p-3 bg-light">
                        <p class="info-title mb-1">Trip Date</p>
                        <p class="info-value mb-0">{{ \Carbon\Carbon::parse($TrucksRoute->route_date)->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- CUSTOMERS --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="info-card p-3">
                        <p class="info-title mb-1">Going Customer</p>
                        <p class="info-value mb-0">{{ ucfirst($TrucksRoute->going_customer) }}</p>
                        <p class="info-title mb-1">Going Fee</p>
                        <p class="info-value mb-0">{{ number_format($TrucksRoute->going_transport_fee ?? 0, 2) }} TZS</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card p-3">
                        <p class="info-title mb-1">Return Customer</p>
                        <p class="info-value mb-0">{{ ucfirst($TrucksRoute->return_customer) }}</p>
                        <p class="info-title mb-1">Return Fee</p>
                        <p class="info-value mb-0">{{ number_format($TrucksRoute->return_transport_fee ?? 0, 2) }} TZS</p>
                    </div>
                </div>
            </div>

            {{-- SUMMARY --}}
            @php
                $total_transport_fee = $TrucksRoute->total_fee;
                $total_expenses = $ExpensesRecord->sum('amount_used');
                $total_route_plan = $RoutePlan->sum('amount_tsh');
                $total_costs = $total_expenses + $total_route_plan;
                $balance = $total_transport_fee - $total_costs;
            @endphp

            <div class="row">
                <div class="col-md-2">
                    <div class="info-card p-3 text-center bg-light">
                        <h6 class="text-primary mb-1">Total Transport Fee</h6>
                        <h5 class="mb-0 text-success">{{ number_format($total_transport_fee, 2) }} TZS</h5>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="info-card p-3 text-center bg-light">
                        <h6 class="text-primary mb-1">Total Route Fuel</h6>
                        <h5 class="mb-0 text-info">{{ number_format($total_route_plan, 2) }} TZS</h5>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="info-card p-3 text-center bg-light">
                        <h6 class="text-primary mb-1">Total Expenses</h6>
                        <h5 class="mb-0 text-warning">{{ number_format($total_expenses, 2) }} TZS</h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-card p-3 text-center {{ $balance >= 0 ? 'bg-primary text-white' : 'bg-danger text-white' }}">
                        <h6 class="mb-1">Balance Remaining (Trip)</h6>
                        <h5 class="mb-0">{{ number_format($balance, 2) }} TZS</h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-card p-3 text-center {{ $balance >= 0 ? 'bg-success text-white' : 'bg-danger text-white' }}">
                        <h6 class="mb-1">Balance Remaining (Month)</h6>
                        <h5 class="mb-0">{{ number_format($balanceRemainingMonth, 2) }} TZS</h5>
                    </div>
                </div>
            </div>

            {{-- TRUCK INFO --}}
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="info-card p-3">
                        <p class="info-title mb-1">Truck Details</p>
                        <p class="info-value mb-0">Plate No: {{ $TrucksRoute->our_truck->plate_no ?? 'N/A' }}</p>
                        <p class="info-value mb-0">Driver: {{ $TrucksRoute->our_truck->driver->first_name ?? 'Unassigned' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card p-3">
                        <p class="info-title mb-1">Created Info</p>
                        <p class="info-value mb-0">By: {{ $TrucksRoute->user->first_name }}</p>
                        <p class="info-value mb-0">On: {{ \Carbon\Carbon::parse($TrucksRoute->created_date)->format('d M Y, h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <ul class="nav nav-tabs card-header-tabs" id="routeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="route-tab" data-bs-toggle="tab" data-bs-target="#routePlans" type="button" role="tab">Route Plans</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="expense-tab" data-bs-toggle="tab" data-bs-target="#expenses" type="button" role="tab">Expenses</button>
                </li>
            </ul>
        </div>

        <div class="card-body tab-content" id="routeTabsContent">
            {{-- ROUTE PLANS TAB --}}
            <div class="tab-pane fade show active" id="routePlans" role="tabpanel">
                <div class="d-flex justify-content-end mb-3">
                    @if($TrucksRoute->status == 'Pending')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newModal_route_plan">
                        + Add Route Plan
                    </button>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>#</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Distance (KM)</th>
                                <th>Fuel (Litres)</th>
                                <th>Amount (TZS)</th>
                                <th>Description</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($RoutePlan as $index => $plan)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ ucfirst($plan->from_location) }}</td>
                                    <td>{{ ucfirst($plan->to_location) }}</td>
                                    <td>{{ number_format($plan->distance_km, 0) }}</td>
                                    <td>{{ number_format($plan->fuel_litres, 0) }}</td>
                                    <td>{{ number_format($plan->amount_tsh, 2) }}</td>
                                    <td>{{ $plan->description }}</td>
                                    <td>{{ $plan->user->first_name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($plan->created_at)->format('d M Y H:i') }}</td>
                                    <td>
                                        @if($TrucksRoute->status == 'Pending')
                                        <div class="pull-right">
                                            <a href="{!! Route('delete-unsubmited-plam', ['id' => $plan->id, 'status' => 'Inactive']) !!}" 
                                               class="btn btn-outline-danger btn-xs"
                                               onclick="return confirm('Are you sure you want to delete all inventory data?')">
                                               Delete <i class="icofont icofont-ui-delete"></i>
                                            </a>
                                          </div>

                                          @endif
                                        </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">No route plans found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- EXPENSES TAB --}}
            <div class="tab-pane fade" id="expenses" role="tabpanel">
                <div class="d-flex justify-content-end mb-3">
                    @if($TrucksRoute->status == 'Pending')
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newModal_expense">
                        + Record Expense
                    </button>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>#</th>
                                <th>Expense Name</th>
                                <th>Amount Used</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ExpensesRecord as $b)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ strtoupper($b->expense->e_name) }}</td>
                                    <td>{{ number_format($b->amount_used, 2) }}</td>
                                    <td>{{ strtoupper($b->desr) }}</td>
                                    <td>{{ $b->status }}</td>
                                    <td>{{ $b->user->first_name }}</td>

                                    <td> 
                                        @if($TrucksRoute->status == 'Pending')
                                        <div class="pull-right">
                                        <a href="{!! Route('delete-unsubmited-expense', ['id' => $b->id, 'status' => 'Inactive']) !!}" 
                                           class="btn btn-outline-danger btn-xs"
                                           onclick="return confirm('Are you sure you want to delete all inventory data?')">
                                           Delete <i class="icofont icofont-ui-delete"></i>
                                        </a>
                                      </div>
                                      @endif
                                    
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No expenses found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- =============== MODALS =============== --}}


@endsection

    <!-- NEW MODAL START -->
    <div class="modal fade" id="newModal_expense" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Expense Registration</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close" ></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ Route('record-expense-truck') }}">
                        @csrf
                        <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-form-label">Expenses</label>
                                <select class="form-control" required name="expense_id">
                                <option value="">--- Choose Expense ---</option>  
                                            @foreach ($Expense as $st)
                                        <option value='{{$st->id}}'>{{strtoupper($st->e_name)}}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
    
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-form-label">Amount Used</label>
                                <input class="form-control" type="number" required name="amount_used" >
                            </div>
                        </div>
                       
                        
                    </div>
    
                    <div class="row">
                    <div class="col-lg-12">
                            <div class="form-group">
                                <label class="col-form-label">Details</label>
                                <textarea class="form-control" name="desr" rows="3" placeholder="Briefly describe ...">na</textarea>
                            </div>
                            
                        </div>
                       <input type="text" hidden value="{{ $TrucksRoute->id }}" name="inventory_id">
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
    
    {{-- END MODAL --}}
    
       <!-- NEW MODAL START -->
    <div class="modal fade" id="newModal_route_plan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Route Plan</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
    
                <div class="modal-body">
                    <form method="post" action="{{ Route('record-route-plan') }}">
                        @csrf
                        <div class="row">
                            <!-- FROM -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="col-form-label">From</label>
                                    <input class="form-control" type="text" name="from_location" required placeholder="Enter starting point">
                                </div>
                            </div>
    
                            <!-- TO -->
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="col-form-label">To</label>
                                    <input class="form-control" type="text" name="to_location" required placeholder="Enter destination">
                                </div>
                            </div>
                        </div>
    
                        <div class="row mt-3">
                            <!-- Distance -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="col-form-label">Distance (KM)</label>
                                    <input class="form-control" type="number" name="distance_km" required placeholder="e.g. 350">
                                </div>
                            </div>
    
                            <!-- Fuel -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="col-form-label">Fuel (Litres)</label>
                                    <input class="form-control" type="number" name="fuel_litres" step="0.01" required placeholder="e.g. 120">
                                </div>
                            </div>
    
                            <!-- Amount -->
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="col-form-label">Amount (Tsh)</label>
                                    <input class="form-control" type="number" name="amount_tsh" required placeholder="e.g. 250000">
                                </div>
                            </div>
                        </div>
    
                        <div class="row mt-3">
                            <!-- Description -->
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="col-form-label">Remarks / Details</label>
                                    <textarea class="form-control" name="description" rows="3" placeholder="Briefly describe route purpose..."></textarea>
                                </div>
                            </div>
    
                            <!-- Hidden Truck Route ID -->
                            <input type="hidden" value="{{ $TrucksRoute->id }}" name="inventory_id">
                        </div>
    
                        <div class="modal-footer mt-4">
                            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                            <button class="btn btn-primary" type="submit">Confirm & Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->
