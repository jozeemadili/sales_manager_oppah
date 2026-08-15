@extends('layouts.admin.master')

@section('title')
    {{ ucfirst(str_replace('-', ' ', Route::currentRouteName())) }}
@endsection

@push('css')
@endpush

@section('content')

    @component('components.breadcrumb')

        @slot('breadcrumb_action_buttons')
            <li>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">
                    New <i class="icofont icofont-plus-circle"></i>
                </button>
            </li>
        @endslot

    @endcomponent

    <div class="container-fluid">

        {{-- Validation Errors --}}
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                <i class="icon-info-alt txt-danger"></i>
                {{ $error }}
                <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
            </div>
        @endforeach

        {{-- Success Message --}}
        @if (session('success'))
            <div class="alert alert-success outline alert-dismissible fade show" role="alert">
                <i class="icofont icofont-check-circled"></i>
                {!! session('success') !!}
                <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
            </div>
        @endif


        @livewire('tuli-sales-management.quick-sale')

    </div>

@endsection

@push('scripts')

    <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>

    <script>
        Livewire.on('InvoiceUpdated', () => {
            $('#newModal').modal('hide');

            setTimeout(() => {
                window.history.back();
            }, 500);
        });

        window.addEventListener('swal:modal', event => {
            swal({
                title: event.detail.message,
                text: event.detail.text,
                icon: event.detail.type,
                buttons: false,
                customClass: 'swal-wide'
            });
        });

        window.addEventListener('swal:confirm', event => {
            swal({
                title: event.detail.message,
                text: event.detail.text,
                icon: event.detail.type,
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    Livewire.emit('remove');
                }
            });
        });

        window.addEventListener('show-print-button', event => {
            const btn = document.createElement('a');
            btn.href = event.detail.url;
            btn.target = '_blank';
            btn.className = 'btn btn-outline-primary btn-xs mt-2';
            btn.innerHTML = 'Print Invoice <i class="icofont icofont-printer"></i>';

            const alertContainer = document.querySelector('.swal-modal');
            if (alertContainer) {
                const div = document.createElement('div');
                div.classList.add('mt-3');
                div.appendChild(btn);
                alertContainer.appendChild(div);
            }
        });
    </script>

<script>

    window.addEventListener('open-discount-modal', event => {
    
        let modal = new bootstrap.Modal(
            document.getElementById('discountModal')
        );
    
        modal.show();
    
    });
    
    
    
    window.addEventListener('close-discount-modal', event => {
    
        let modalElement =
        document.getElementById('discountModal');
    
    
        let modal =
        bootstrap.Modal.getInstance(modalElement);
    
    
        if(modal)
        {
            modal.hide();
        }
    
    });
    
    </script>

@endpush