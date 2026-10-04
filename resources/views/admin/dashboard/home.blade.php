@extends('layouts.admin.master')
@section('title', 'Dashboard')

@push('css')
<style>
    .dashboard-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .dashboard-header h4 {
        font-weight: 600;
        color: #333;
    }

    .dashboard-section {
        margin-top: 25px;
    }

    .card {
        box-shadow: 0px 2px 8px rgba(0,0,0,0.05);
        border: none;
    }

    .income-card .card-body h5 {
        font-weight: 700;
        font-size: 1.5rem;
    }

    .income-card .card-body p {
        color: #777;
        font-size: 0.9rem;
    }

    .round-box {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 65px;
        height: 65px;
        margin-bottom: 10px;
    }

    hr.section-divider {
        border-top: 2px solid #eee;
        margin: 35px 0 20px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid dashboard-default-sec">

    <!-- 🧾 Summary Section -->
    <div class="dashboard-section">
        <div class="dashboard-header mb-3">
            <h4><i class="icofont icofont-chart-histogram"></i> Business Overview</h4>
        </div>
        @livewire('components.reports.summary')
    </div>

</div>
@endsection
