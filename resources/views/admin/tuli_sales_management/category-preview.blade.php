@extends('layouts.admin.master')

@section('title')
    {{ ucfirst(str_replace('-', ' ', Route::currentRouteName())) }}
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('breadcrumb_title')
            <h3>{{ ucfirst(str_replace('-', ' ', Route::currentRouteName())) }}</h3>
        @endslot
        <li class="breadcrumb-item">{{ ucfirst(explode('-', Route::currentRouteName())[0]) }}</li>
        <li class="breadcrumb-item active">{{ ucfirst(explode('-', Route::currentRouteName())[1]) }}</li>
    @endcomponent

    <div class="container-fluid">

        @foreach ($errors->all() as $error)
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="icon-info-alt txt-danger"></i> {{ $error }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endforeach

        @if($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="icofont icofont-check-circled"></i> {!! $message !!}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            @if(isset($Products) && count($Products) > 0)
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3">Category Summary</h5>

                            <div class="row mb-4">
<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header bg-primary text-white">General Summary</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total Products</span>
                    <strong>{{ count($AllProducts) }}</strong>
                </li>
                @if(Auth::user()->role == 'ADMIN' )
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total Stock Value</span>
                    <strong>
                        {{ number_format($AllProducts->sum(fn($p) => $p->qty_remained * $p->purchasing_price), 2) }}
                    </strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Expected Profit</span>
                    <strong>
                        {{ number_format($AllProducts->sum(fn($p) => ($p->qty * $p->selling_price) - ($p->qty * $p->purchasing_price)), 2) }}
                    </strong>
                </li>
                @endif
            </ul>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header bg-success text-white">Stock Summary</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total Quantity</span>
                    <strong>{{ number_format($AllProducts->sum('qty'), 2) }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total Quantity Remaining</span>
                    <strong>{{ number_format($AllProducts->sum('qty_remained'), 2) }}</strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total Sold Quantity </span>
                    <strong>
                        {{ number_format($AllProducts->sum('qty')-$AllProducts->sum('qty_remained'), 2) }}
                    </strong>
                </li>
            </ul>
        </div>
    </div>
</div>

                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>#</th>
                                            <th>Product Name</th>
                                            <th>Category</th>
                                            <th>Store</th>
                                            <th>Barcode</th>
                                            <th>Qty Recorded</th>
                                            <th>Qty Remained</th>
                                            @if(Auth::user()->role == 'ADMIN' )
                                            <th>Purchasing Price</th>
                                            @endif
                                            <th>Selling Price</th>
                                            @if(Auth::user()->role == 'ADMIN' )
                                            <th>Expected Profit/Loss</th>
                                            @endif
                                            
                                            <th>Status</th>
                                            <th>Created By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($Products as $product)
                                            @php
                                                $profit = ($product->qty * $product->selling_price) - ($product->qty * $product->purchasing_price);
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ strtoupper($product->product_name) }}</td>
                                                <td>{{ strtoupper($product->Category->name) }}</td>
                                                <td>{{ strtoupper($product->Store->name) }}</td>
                                                <td>{{ $product->barcode }}</td>
                                                <td>{{ number_format($product->qty, 2) }}</td>
                                                
                                                <td>{{ number_format($product->qty_remained, 2) }}</td>
                                                @if(Auth::user()->role == 'ADMIN' )
                                                <td>{{ number_format($product->purchasing_price, 2) }}</td>
                                                @endif
                                                <td>{{ number_format($product->selling_price, 2) }}</td>
                                                @if(Auth::user()->role == 'ADMIN' )
                                                <td style="color: {{ $profit < 0 ? 'red' : 'green' }};">
                                                    {{ number_format($profit, 2) }}
                                                </td>
                                                @endif
                                                <td>{{ $product->status }}</td>
                                                <td>{{ $product->user->first_name }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $Products->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="icon-info-alt"></i> No Records Found.
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    </div>
@endsection
