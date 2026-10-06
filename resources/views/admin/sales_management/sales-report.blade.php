@extends('layouts.admin.master')

@section('title')
    Sales Report
@endsection

@section('content')
    <div class="container-fluid">
        @foreach ($errors->all() as $error)
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="icon-info-alt txt-danger"></i> {{ $error }}
                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endforeach

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="icofont icofont-check-circled"></i> {!! session('success') !!}
                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Filter Form -->
        <div class="card mb-3">
            <div class="card-body">
                <form id="filterForm" method="GET" action="{{ route('sales-report') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label>Product Name:</label>
                            <input type="text" name="product_name" class="form-control" value="{{ request('product_name') }}">
                        </div>
                        <div class="col-md-3">
                            <label>Customer Name:</label>
                            <input type="text" name="customer_name" class="form-control" value="{{ request('customer_name') }}">
                        </div>
                        <div class="col-md-3">
                            <label>Filter By:</label>
                            @php($filterType = request()->filled('start_date') ? 'date' : (request()->filled('start_month') ? 'month' : ''))
                            <select id="filter_type" class="form-control">
                                <option value="">Select Type</option>
                                <option value="date" @selected($filterType === 'date')>Date</option>
                                <option value="month" @selected($filterType === 'month')>Month</option>
                            </select>
                        </div>
                        <div id="date_filter" class="col-md-3 {{ $filterType === 'date' ? '' : 'd-none' }}">
                            <label>Start Date:</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            <label>End Date:</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div id="month_filter" class="col-md-3 {{ $filterType === 'month' ? '' : 'd-none' }}">
                            <label>Start Month:</label>
                            <input type="month" name="start_month" class="form-control" value="{{ request('start_month') }}">
                            <label>End Month:</label>
                            <input type="month" name="end_month" class="form-control" value="{{ request('end_month') }}">
                        </div>
                       
                        <div class="col-md-3">
                            <label>Store:</label>
                            <select name="store_id" class="form-control">
                                <option value="all" @selected($storeId === null)>All stores</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" @selected($storeId === $store->id)>{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Invoice Status:</label>
                            <select name="status" class="form-control">
                                <option value="">Select Status</option>
                                <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                                <option value="Confirmed" {{ request('status') == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>
                       
                      
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv', 'page' => null]) }}" class="btn btn-success">
                                <i class="fa fa-download"></i> Download CSV
                            </a>
                            <button type="button" id="clearForm" class="btn btn-outline-danger">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-3 row-cols-xl-5 g-2 mb-3">
            <div class="col"><div class="card mb-0 h-100"><div class="card-body p-3">
                <div class="small text-muted">Invoiced value (paid + unpaid)</div>
                <div class="fw-bold fs-5">{{ number_format($totalAmount, 0) }}</div>
                <div class="small text-muted">{{ number_format($totalQty, 2) }} pcs on invoices</div>
            </div></div></div>
            <div class="col"><div class="card mb-0 h-100"><div class="card-body p-3">
                <div class="small text-muted">Quick sales (walk-in)</div>
                <div class="fw-bold fs-5">{{ number_format($quickSales, 0) }}</div>
                <div class="small text-muted">{{ number_format($quickQty, 2) }} pcs, listed as "Walk-in"</div>
            </div></div></div>
            <div class="col"><div class="card mb-0 h-100 border-dark"><div class="card-body p-3">
                <div class="small text-muted">Total sales (invoiced + quick sales)</div>
                <div class="fw-bold fs-5">{{ number_format($totalAmount + $quickSales, 0) }}</div>
                <div class="small text-muted">{{ number_format($totalQty + $quickQty, 2) }} pcs</div>
            </div></div></div>
            <div class="col"><div class="card mb-0 h-100"><div class="card-body p-3">
                <div class="small text-muted">Amount received on these invoices</div>
                <div class="fw-bold fs-5 text-success">{{ number_format($amountReceived, 0) }}</div>
                <div class="small text-muted">Not yet paid: {{ number_format(max($totalAmount - $amountReceived, 0), 0) }}</div>
            </div></div></div>
            <div class="col"><div class="card mb-0 h-100 border-primary"><div class="card-body p-3">
                <div class="small text-muted">Total received (received + quick sales)</div>
                <div class="fw-bold fs-5 text-primary">{{ number_format($amountReceived + $quickSales, 0) }}</div>
                <div class="small text-muted">Matches the dashboard graph for Mzinga</div>
            </div></div></div>
        </div>

        <!-- Invoice Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Sold Quantity</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Date Paid</th>
                                <th>Paid By</th>
                               
                             
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                              <tr>
                                  <th scope="row">{{ $sales->firstItem() + $loop->index }}</th>
                                  <td>{{ $sale->product_name }}</td>
                                  <td>
                                      {{ $sale->customer ?? 'N/A' }}
                                      @if($sale->source === 'quick') <span class="badge bg-light text-dark border">Quick sale</span> @endif
                                  </td>
                                  <td>{{ $sale->sold_at ? \Carbon\Carbon::parse($sale->sold_at)->format('d/m/Y H:i') : '' }}</td>
                                  <td>{{ number_format($sale->qty, 2) }}</td>
                                  <td>{{ number_format($sale->amount, 2) }}</td>
                                  <td>{{ $sale->status }}</td>
                                  <td>{{ $sale->date_paid ? \Carbon\Carbon::parse($sale->date_paid)->format('d/m/Y H:i') : '' }}</td>
                                  <td>{{ $sale->paid_by }}</td>

                                 
                                  
                              </tr>
                             
                            @endforeach
                            <tfoot>
        <tr>
            <td colspan="4"><strong>Total ({{ $sales->total() }} rows)</strong></td>
            <td><strong>{{ number_format($totalQty + $quickQty, 2) }}</strong></td>
            <td><strong>{{ number_format($totalAmount + $quickSales, 2) }} TZS</strong></td>
        </tr>
    </tfoot>
                        </tbody>
                    </table>
                    {{ $sales->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('filter_type').addEventListener('change', function () {
            document.getElementById('date_filter').classList.add('d-none');
            document.getElementById('month_filter').classList.add('d-none');
            if (this.value === 'date') document.getElementById('date_filter').classList.remove('d-none');
            if (this.value === 'month') document.getElementById('month_filter').classList.remove('d-none');
        });

        document.getElementById('clearForm').addEventListener('click', function () {
            document.getElementById('filterForm').reset();
            window.location.href = '{{ route('sales-report') }}';
        });

        document.getElementById('filterForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default submission

            let form = event.target;
            let formData = new FormData(form);
            let searchParams = new URLSearchParams();

            // Only add non-empty fields to the search query
            formData.forEach((value, key) => {
                if (value.trim() !== '') {
                    searchParams.append(key, value);
                }
            });

            // Redirect with clean query parameters
            window.location.href = form.action + '?' + searchParams.toString();
        });

    </script>
@endsection
