@extends('layouts.admin.master')
@section('title')
Daily Expenses
@endsection

@section('content')
  @component('components.breadcrumb')
    @slot('breadcrumb_title')
      <h3>Daily Expenses</h3>
    @endslot

    @slot('breadcrumb_action_buttons')
      <li><button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#typeModal">Expense Types <i class="icofont icofont-tags"></i></button></li>
      <li><button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newModal">New Expense <i class="icofont icofont-plus-circle"></i></button></li>
    @endslot

    <li class="breadcrumb-item">Expenses</li>
    <li class="breadcrumb-item active">Daily</li>
  @endcomponent

  <div class="container-fluid">
      <div class="row">
          <div class="col-sm-12">
              <div class="card">
                  <div class="card-body">
                    @foreach ($errors->all() as $error)
                    <div class="alert alert-danger outline alert-dismissible fade show" role="alert">
                        <i class="icon-info-alt txt-danger"></i> {{ $error }}
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endforeach

                    @if($message = Session::get('success'))
                    <div class="alert alert-success outline alert-dismissible fade show" role="alert">
                        <i class="icofont icofont-check-circled"></i> {!! $message !!}
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <!-- Filters -->
                    <form method="get" action="{{ route('daily-expenses') }}" class="row g-2 align-items-end mb-3">
                        <div class="col-md-3">
                            <label class="col-form-label">From</label>
                            <input class="form-control" type="date" name="from" value="{{ $from }}">
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label">To</label>
                            <input class="form-control" type="date" name="to" value="{{ $to }}">
                        </div>
                        @if(Auth::user()->hasFullAccess())
                        <div class="col-md-3">
                            <label class="col-form-label">Store</label>
                            <select class="form-select" name="store_id">
                                <option value="all">All Stores</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" @selected((string) $storeId === (string) $store->id)>{{ strtoupper($store->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-md-3">
                            <button class="btn btn-outline-primary w-100" type="submit">Filter <i class="icofont icofont-filter"></i></button>
                        </div>
                    </form>

                    <div class="alert alert-light border d-flex justify-content-between align-items-center">
                        <span><strong>Total for period:</strong> {{ \Carbon\Carbon::parse($from)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</span>
                        <strong class="fs-5">{{ number_format($total, 0) }} TZS</strong>
                    </div>

                    <div class="table-responsive">
                        @if($expenses->count())
                        <table class="table table-xs table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Expense</th>
                                    <th>Store</th>
                                    <th>Description</th>
                                    <th class="text-end">Amount (TZS)</th>
                                    <th>Recorded By</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenses as $expense)
                                <tr>
                                    <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                                    <td>{{ strtoupper(optional($expense->type)->name) }}</td>
                                    <td>{{ strtoupper(optional($expense->store)->name) }}</td>
                                    <td><small>{{ $expense->description }}</small></td>
                                    <td class="text-end">{{ number_format($expense->amount, 0) }}</td>
                                    <td><small>{{ optional($expense->user)->first_name }}</small></td>
                                    <td class="text-end">
                                        @if($expense->canBeChangedBy(Auth::user()))
                                        <button class="btn btn-outline-primary btn-xs" data-bs-toggle="modal" data-bs-target="#editModal{{ $expense->id }}">Edit</button>
                                        <form method="post" action="{{ route('daily-expenses-delete', $expense->id) }}" class="d-inline" onsubmit="return confirm('Delete this expense?');">
                                            @csrf
                                            <button class="btn btn-outline-danger btn-xs" type="submit">Delete</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $expenses->links() }}
                        @else
                        <div class="alert alert-info outline" role="alert">
                            <i class="icon-info-alt"></i> No daily expenses recorded for this period.
                        </div>
                        @endif
                    </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <!-- NEW EXPENSE MODAL -->
  <div class="modal fade" id="newModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Record Daily Expense</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="{{ route('daily-expenses-store') }}">
                @csrf
                <div class="modal-body">
                    @include('admin.sales_management.partials.daily-expense-fields', ['expense' => null])
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Save Expense</button>
                </div>
            </form>
        </div>
    </div>
  </div>

  <!-- EDIT MODALS -->
  @foreach($expenses as $expense)
    @if($expense->canBeChangedBy(Auth::user()))
    <div class="modal fade" id="editModal{{ $expense->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit Daily Expense</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{ route('daily-expenses-update', $expense->id) }}">
                    @csrf
                    <div class="modal-body">
                        @include('admin.sales_management.partials.daily-expense-fields', ['expense' => $expense])
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary" type="submit">Update Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
  @endforeach

  <!-- EXPENSE TYPES MODAL -->
  <div class="modal fade" id="typeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Daily Expense Types</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($types->count())
                <ul class="list-group mb-3">
                    @foreach($types as $type)
                    <li class="list-group-item py-1">{{ strtoupper($type->name) }}</li>
                    @endforeach
                </ul>
                @else
                <p class="text-muted">No types yet. Add the first one below (e.g. UMEME, CHAKULA, VIBARUA).</p>
                @endif
                <form method="post" action="{{ route('daily-expense-types-store') }}">
                    @csrf
                    <label class="col-form-label">New Type</label>
                    <div class="input-group">
                        <input class="form-control" type="text" name="name" required maxlength="200" placeholder="e.g. UMEME">
                        <button class="btn btn-primary" type="submit">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
  </div>
@endsection
