@extends('layouts.admin.master')

@section('title')
Truck Routes Report
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/date-picker.css') }}">

<style>
    .summary-card {
        border-radius: 10px;
        padding: 18px;
        box-shadow: 0px 2px 8px rgba(0,0,0,0.05);
        transition: 0.3s ease-in-out;
    }

    .summary-card:hover {
        transform: translateY(-3px);
    }

    .summary-title {
        font-size: 14px;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .summary-value {
        font-size: 22px;
        font-weight: 700;
    }

    .balance-positive { color: #28a745 !important; }
    .balance-negative { color: #dc3545 !important; }

    .report-table td, .report-table th {
        vertical-align: middle;
        font-size: 13px;
    }

    .truck-info small {
        color: #6c757d;
        font-size: 11px;
    }

    .filter-badge {
        background: #e3f2fd;
        border-left: 4px solid #0d6efd;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 15px;
    }
</style>
@endpush

@section('content')

@component('components.breadcrumb')
    @slot('breadcrumb_title')
        <h3><i class="icofont icofont-chart-histogram"></i> Truck Routes Report</h3>
    @endslot

    @slot('breadcrumb_action_buttons')
        <li>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="icofont icofont-search-alt-1"></i> Filter
            </button>
        </li>
    @endslot

    <li class="breadcrumb-item">Reports</li>
    <li class="breadcrumb-item active">Truck Routes</li>
@endcomponent


<div class="container-fluid">
    <div class="row">

        <div class="col-sm-12">

            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Summary Overview</h5>
                    @if($selectedTruck || $selectedMonth)
                        <span class="badge bg-info">Filtered</span>
                    @endif
                </div>

                <div class="card-body">

                    {{-- =============== FILTER SUMMARY DISPLAY =============== --}}
                    <div class="filter-badge">
                        <strong><i class="icofont icofont-filter"></i> Applied Filters:</strong><br>

                        {{-- Truck Filter --}}
                        <strong>Truck:</strong>
                        @if($selectedTruck)
                            @php
                                $truck = $trucks->where('id', $selectedTruck)->first();
                            @endphp
                            {{ $truck->plate_no ?? 'Unknown' }} 
                            — Driver: {{ $truck->driver->first_name ?? 'N/A' }}
                        @else
                            <em>All Trucks</em>
                        @endif
                        <br>

                        {{-- Month Filter --}}
                        <strong>Month:</strong>
                        @if($selectedMonth)
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}
                        @else
                            <em>Any Month</em>
                        @endif
                    </div>

                    {{-- Summary Cards --}}
                    <div class="row g-3">

                        <div class="col-md-3">
                            <div class="summary-card bg-light">
                                <div class="summary-title">Total Transport Fee</div>
                                <div class="summary-value text-success">{{ number_format($totalTransportFee, 2) }} TZS</div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="summary-card bg-light">
                                <div class="summary-title">Total Route Fuel</div>
                                <div class="summary-value text-warning">{{ number_format($totalRouteFuel, 2) }} TZS</div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="summary-card bg-light">
                                <div class="summary-title">Total Expenses</div>
                                <div class="summary-value text-danger">{{ number_format($totalExpenses, 2) }} TZS</div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="summary-card bg-light">
                                <div class="summary-title">Balance Remaining</div>
                                <div class="summary-value 
                                    {{ $balanceRemaining >= 0 ? 'balance-positive' : 'balance-negative' }}">
                                    {{ number_format($balanceRemaining, 2) }} TZS
                                </div>
                            </div>
                        </div>

                    </div>

                    <hr>

                    {{-- ================= TABLE ================= --}}
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-striped report-table">
                            <thead class="table">
                                <tr>
                                    <th>Trip</th>
                                    <th>Date</th>
                                    <th>Truck</th>
                                    <th>Going</th>
                                    <th>Return</th>
                                    <th>Fee</th>
                                    <th>Fuel</th>
                                    <th>Expenses</th>
                                    <th>Balance</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($TrucksRoutes as $route)
                                @php
                                    $routeFuel = $route->routePlans->sum('amount_tsh');
                                    $routeExpenses = $route->expensesRecords->sum('amount_used');
                                    $routeBalance = $route->total_fee - $routeFuel - $routeExpenses;
                                @endphp

                                <tr>
                                    <td><strong>{{ $route->trip_no }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($route->route_date)->format('d M Y') }}</td>

                                    <td class="truck-info">
                                        <strong>{{ $route->our_truck->plate_no ?? 'N/A' }}</strong><br>
                                        <small>{{ $route->our_truck->driver->first_name ?? 'N/A' }}</small>
                                    </td>

                                    <td>{{ $route->going_customer }}</td>
                                    <td>{{ $route->return_customer }}</td>

                                    <td class="text-success">{{ number_format($route->total_fee, 2) }}</td>
                                    <td class="text-warning">{{ number_format($routeFuel, 2) }}</td>
                                    <td class="text-danger">{{ number_format($routeExpenses, 2) }}</td>

                                    <td class="{{ $routeBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                        <strong>{{ number_format($routeBalance, 2) }}</strong>
                                    </td>
                                    <td>
                                        <div class="pull-left"> <a href='{!! Route('route-preview', ['id' => $route->id]) !!}' class='btn btn-outline-info btn-xs'> view </a>
                                            
                                    </td>
                                </tr>

                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div> {{-- card-body --}}
            </div> {{-- card --}}
        </div>
    </div>
</div>


{{-- =================== FILTER MODAL =================== --}}
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <form method="GET" action="{{ route('truck-reports') }}" class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="icofont icofont-filter"></i> Filter Report
                </h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <label class="form-label">Select Truck</label>
                    <select name="truck_id" class="form-control">
                        <option value="">-- Any Truck --</option>
                        @foreach($trucks as $truck)
                            <option value="{{ $truck->id }}" {{ $truck->id == $selectedTruck ? 'selected' : '' }}>
                                {{ $truck->plate_no }} — {{ $truck->driver->first_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Select Month</label>
                    <input type="month" name="month" class="form-control" value="{{ $selectedMonth }}">
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" type="submit">
                    <i class="icofont icofont-search"></i> Apply Filters
                </button>
            </div>

        </form>
    </div>
</div>

@endsection
