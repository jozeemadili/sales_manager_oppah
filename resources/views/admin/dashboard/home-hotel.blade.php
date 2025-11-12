@extends('layouts.admin.master')
@section('title', 'Dashboard')
@push('css')
@endpush
    @section('content')
      <!-- Container-fluid starts-->
      <div class="container-fluid dashboard-default-sec">

      <div class="row">
      <div class="col-lg-12">
            @livewire('components.reports.summary-hotel')
        </div>
      </div>

      <div class="row">
      <div class="col-lg-12">
          @livewire('components.reports.salescharts-hotel')
        </div>
       
      </div>
      

      </div>
      <!-- Container-fluid Ends-->
@endsection
