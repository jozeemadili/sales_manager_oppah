@extends('layouts.admin.master')
@section('title')
Daily Expenses
@endsection

@push('css')
<style>
    .day-card { transition: box-shadow .15s, transform .15s; cursor: pointer; }
    .day-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.08); transform: translateY(-2px); }
</style>
@endpush

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
                        <div class="col-md-2">
                            <label class="col-form-label">From</label>
                            <input class="form-control" type="date" name="from" value="{{ $from }}">
                        </div>
                        <div class="col-md-2">
                            <label class="col-form-label">To</label>
                            <input class="form-control" type="date" name="to" value="{{ $to }}">
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label">Expense</label>
                            <select class="form-select" name="type_id">
                                <option value="">All expenses</option>
                                @foreach($filterTypes as $type)
                                    <option value="{{ $type->id }}" @selected((string) $typeId === (string) $type->id)>{{ strtoupper($type->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="col-form-label">Store</label>
                            <input class="form-control" type="text" value="{{ strtoupper($store->name) }}" readonly>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-primary w-100" type="submit">Filter <i class="icofont icofont-filter"></i></button>
                        </div>
                    </form>

                    <div class="alert alert-light border d-flex justify-content-between align-items-center">
                        <span><strong>Total for period:</strong> {{ \Carbon\Carbon::parse($from)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</span>
                        <strong class="fs-5">{{ number_format($total, 0) }} TZS</strong>
                    </div>

                    <!-- Daily totals: click a card to see that day's entries -->
                    @if($days->count())
                    <h6 class="mb-2">Total per Day <small class="text-muted">(click a day to see its expenses)</small></h6>
                    <div class="row g-2 mb-4">
                        @foreach($days as $date => $entries)
                        <div class="col-6 col-md-4 col-lg-2">
                            <a href="javascript:void(0)" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#dayModal{{ str_replace('-', '', $date) }}">
                                <div class="card border mb-0 h-100 day-card {{ $date === now()->toDateString() ? 'border-primary' : '' }}">
                                    <div class="card-body p-2 text-center">
                                        <div class="small text-muted">{{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}</div>
                                        <div class="fw-bold fs-6 text-dark">{{ number_format($entries->sum('amount'), 0) }}</div>
                                        <div class="small text-muted">{{ $entries->count() }} {{ \Illuminate\Support\Str::plural('entry', $entries->count()) }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <h6 class="mb-2">All Entries</h6>
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
                                    {{-- <th class="text-end">Action</th> --}}
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
                                    {{-- Edit / Delete hidden for now
                                    <td class="text-end">
                                        @if($expense->canBeChangedBy(Auth::user()))
                                        <button class="btn btn-outline-primary btn-xs" data-bs-toggle="modal" data-bs-target="#editModal{{ $expense->id }}">Edit</button>
                                        <form method="post" action="{{ route('daily-expenses-delete', $expense->id) }}" class="d-inline" onsubmit="return confirm('Delete this expense?');">
                                            @csrf
                                            <button class="btn btn-outline-danger btn-xs" type="submit">Delete</button>
                                        </form>
                                        @endif
                                    </td>
                                    --}}
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

  <!-- DAY DETAIL MODALS -->
  @foreach($days as $date => $entries)
  <div class="modal fade" id="dayModal{{ str_replace('-', '', $date) }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Expenses for {{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}</h5>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Expense</th>
                                <th>Store</th>
                                <th>Description</th>
                                <th class="text-end">Amount (TZS)</th>
                                <th>Recorded By</th>
                                <th>Time Recorded</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entries as $entry)
                            <tr>
                                <td>{{ strtoupper(optional($entry->type)->name) }}</td>
                                <td><small>{{ strtoupper(optional($entry->store)->name) }}</small></td>
                                <td><small>{{ $entry->description }}</small></td>
                                <td class="text-end">{{ number_format($entry->amount, 0) }}</td>
                                <td>{{ optional($entry->user)->first_name }}</td>
                                <td><small>{{ \Carbon\Carbon::parse($entry->created_at)->format('d/m/Y H:i') }}</small></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="3">Total</td>
                                <td class="text-end">{{ number_format($entries->sum('amount'), 0) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
  </div>
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
