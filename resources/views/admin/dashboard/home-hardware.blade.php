@extends('layouts.admin.master')
@section('title', 'Dashboard')
@push('css')
@endpush
    @section('content')
      <!-- Container-fluid starts-->
      <div class="container-fluid dashboard-default-sec">

        <div class="dashboard-section">
          <div class="dashboard-header mb-3">
              <h4><i class="icofont icofont-chart-bar-graph"></i> Monthly Sales TRuck</h4>
          </div>
          <div class="row">
              <div class="col-lg-12">
                  @livewire('tuli-sales-management.hardware-dashboard')
                  {{-- resources/views/livewire/tuli-sales-management/hardware-dashboard.blade.php --}}
              </div>
          </div>
      </div>

       

     

      
      
      

      </div>
      <!-- Container-fluid Ends-->
@endsection
