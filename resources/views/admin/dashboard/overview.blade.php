@extends('layouts.admin.master')
@section('title', 'Overview')

@section('content')
<div class="container-fluid dashboard-default-sec">
    <div class="dashboard-section">
        <div class="dashboard-header mb-3">
            <h4><i class="icofont icofont-dashboard-web"></i> Overview &mdash; All Modules</h4>
        </div>
        @livewire('components.reports.overview-dashboard')
    </div>
</div>
@endsection
