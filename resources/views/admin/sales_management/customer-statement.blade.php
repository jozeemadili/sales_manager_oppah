@extends('layouts.admin.master')
@section('title')
Customer Statement
@endsection

@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>Customer Statement</h3>
    @endslot

    @slot('breadcrumb_action_buttons')
      <li><a class="btn btn-outline-secondary" href="{{ route('home') }}"><i class="icofont icofont-arrow-left"></i> Dashboard</a></li>
      @if($invoices->count())
      <li><a class="btn btn-primary" href="{{ route('customer-statement-pdf', $customer->id) }}"><i class="icofont icofont-file-pdf"></i> Download PDF Reminder</a></li>
      @endif
    @endslot

    <li class="breadcrumb-item">Customers</li>
    <li class="breadcrumb-item active">Statement</li>
  @endcomponent

  <div class="container-fluid">
      <div class="row">
          <div class="col-lg-4">
              <div class="card">
                  <div class="card-body">
                      <h5 class="mb-1">{{ strtoupper($customer->name) }}</h5>
                      @if($customer->phone)<div><i class="icofont icofont-phone"></i> +255{{ $customer->phone }}</div>@endif
                      @if($customer->email)<div><i class="icofont icofont-email"></i> {{ $customer->email }}</div>@endif
                      @if($customer->tin)<div><small class="text-muted">TIN: {{ $customer->tin }}</small></div>@endif
                      @if($customer->physical_addres)<div><small class="text-muted">{{ $customer->physical_addres }}</small></div>@endif
                      <hr>
                      <div class="d-flex justify-content-between"><span>Unpaid invoices</span><strong>{{ $invoices->count() }}</strong></div>
                      <div class="d-flex justify-content-between"><span>Total invoiced</span><strong>{{ number_format($totals['total'], 0) }}</strong></div>
                      <div class="d-flex justify-content-between"><span>Total paid</span><strong>{{ number_format($totals['paid'], 0) }}</strong></div>
                      <div class="d-flex justify-content-between fs-5 mt-2 text-danger"><span>Balance due</span><strong>{{ number_format($totals['balance'], 0) }} TZS</strong></div>
                  </div>
              </div>
          </div>

          <div class="col-lg-8">
              <div class="card">
                  <div class="card-header"><h5 class="mb-0">Unpaid Invoices</h5></div>
                  <div class="card-body p-0">
                      <div class="table-responsive">
                          <table class="table table-sm table-hover mb-0 align-middle">
                              <thead>
                                  <tr>
                                      <th>#</th>
                                      <th>Invoice No</th>
                                      <th>Invoice Date</th>
                                      <th class="text-end">Age (days)</th>
                                      <th>Status</th>
                                      <th class="text-end">Total</th>
                                      <th class="text-end">Paid</th>
                                      <th class="text-end">Remaining</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  @forelse($invoices as $invoice)
                                  <tr>
                                      <td>{{ $loop->iteration }}</td>
                                      <td><a href="{{ route('invoice-preview', $invoice->id) }}" target="_blank">{{ \App\Http\Controllers\Stock\CustomerStatementController::invoiceNumber($invoice) }}</a></td>
                                      <td>{{ optional($invoice->invoice_date)->format('d M Y') }}</td>
                                      <td class="text-end">{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->startOfDay()->diffInDays(today()) : '' }}</td>
                                      <td><span class="badge bg-light text-dark border">{{ $invoice->status }}</span></td>
                                      <td class="text-end">{{ number_format($invoice->total_invoice_amount, 0) }}</td>
                                      <td class="text-end">{{ number_format($invoice->amount_paid, 0) }}</td>
                                      <td class="text-end fw-semibold">{{ number_format($invoice->amount_remained, 0) }}</td>
                                  </tr>
                                  @empty
                                  <tr><td colspan="8" class="text-center text-muted py-4">This customer has no unpaid invoices.</td></tr>
                                  @endforelse
                              </tbody>
                              @if($invoices->count())
                              <tfoot>
                                  <tr class="fw-bold">
                                      <td colspan="5">Total</td>
                                      <td class="text-end">{{ number_format($totals['total'], 0) }}</td>
                                      <td class="text-end">{{ number_format($totals['paid'], 0) }}</td>
                                      <td class="text-end text-danger">{{ number_format($totals['balance'], 0) }}</td>
                                  </tr>
                              </tfoot>
                              @endif
                          </table>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
@endsection
