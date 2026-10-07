@extends('layouts.admin.master')

@section('title')
{{ ucfirst(str_replace('-',' ',Route::currentRouteName())) }}
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/date-picker.css') }}">
<style>
    .preview-box {
        border: 1px solid #ddd;
        padding: 10px;
        margin-top: 10px;
        display: inline-block;
        position: relative;
        margin-right: 10px;
    }
    .preview-box img {
        max-width: 120px;
        max-height: 120px;
    }
    .remove-btn {
        position: absolute;
        top: -8px;
        right: -8px;
        background: red;
        color: #fff;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 10px;
        border: none;
    }
</style>
@endpush

@section('content')

@component('components.breadcrumb')
    @slot('breadcrumb_title')
        <h3>{{ ucfirst(str_replace('-',' ',Route::currentRouteName())) }}</h3>
    @endslot

    @slot('breadcrumb_action_buttons')
        <li>
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newModal">
                New Logistics Deposit <i class="icofont icofont-plus-circle"></i>
            </button>
        </li>
    @endslot

    <li class="breadcrumb-item">{{ ucfirst(explode('-', Route::currentRouteName())[0]) }}</li>
    <li class="breadcrumb-item active">{{ ucfirst(explode('-', Route::currentRouteName())[1]) }}</li>
@endcomponent


<div class="container-fluid">
<div class="row">
<div class="col-sm-12">

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card">
    <div class="card-header">
        <h5>Bank Deposits</h5>

        {{-- Logistics deposits are added on this page; Mbao deposits come from the Mbao dashboard. --}}
        <ul class="nav nav-pills mt-2">
            @foreach(['logistics' => 'Logistics', 'mbao' => 'Mbao', 'all' => 'All'] as $key => $label)
                <li class="nav-item me-2">
                    <a class="nav-link {{ $type === $key ? 'active' : 'border' }}" href="{{ request()->fullUrlWithQuery(['type' => $key, 'page' => null]) }}">
                        {{ $label }} <span class="badge {{ $type === $key ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">{{ number_format($typeTotals[$key], 0) }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <h6 class="text-success mt-3">
            <strong>Total {{ ['logistics' => 'Logistics', 'mbao' => 'Mbao', 'all' => 'All'][$type] }} Deposits{{ request()->anyFilled(['from', 'to', 'bank', 'account']) ? ' (filtered)' : '' }}:</strong>
            {{ number_format($totalDeposits, 2) }}
        </h6>
        @if($type === 'mbao')
            <small class="text-muted d-block">Mbao deposits are recorded from the Mbao dashboard (Balance card &rarr; Bank deposit).</small>
        @endif

        <form method="GET" action="{{ route('bank-deposit') }}" class="row g-2 align-items-end mt-2">
            <input type="hidden" name="type" value="{{ $type }}">
            <div class="col-md-2">
                <label class="small mb-0">From</label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>
            <div class="col-md-2">
                <label class="small mb-0">To</label>
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>
            <div class="col-md-3">
                <label class="small mb-0">Bank</label>
                <select name="bank" class="form-control" onchange="this.form.account && (this.form.account.value = '')">
                    <option value="">All banks</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank }}" @selected(strtoupper(trim(request('bank', ''))) === $bank)>{{ $bank }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="small mb-0">Account</label>
                <select name="account" class="form-control">
                    <option value="">All accounts</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account }}" @selected(request('account') === $account)>{{ $account }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                <a href="{{ route('bank-deposit', ['type' => $type]) }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
    

<div class="card-body table-responsive">

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Type</th>
                <th>Source / Origin </th>
                <th>Bank Deposited</th>
                <th>Account</th>
                <th>Amount Deposited</th>
                <th>Date Deposited</th>
                <th>status</th>
                <th>Slip</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($deposits as $deposit)
            <tr>
                <td>{{ $deposit->id }}</td>
                <td>
                    @if($deposit->isMbao())
                        <span class="badge bg-success">Mbao</span>
                    @else
                        <span class="badge bg-primary">Logistics</span>
                    @endif
                </td>
                <td>{{ $deposit->deposit_origin }}</td>
                <td>{{ strtoupper(trim($deposit->bank_name)) }}</td>
                <td>{{ $deposit->account_number ?: '—' }}</td>
                <td>{{ number_format($deposit->deposited_amount, 2) }}</td>
                <td>{{ $deposit->deposited_date->format('Y-m-d') }}</td>
                <td>{{ $deposit->status }}</td>
                
                <td>
                    @forelse ($deposit->files as $file)
                        @if(Str::endsWith($file->file_path, ['jpg','jpeg','png']))
                            <a href="{{ $file->file_url }}" target="_blank">
                                <img src="{{ $file->file_url }}" width="70" class="img-thumbnail mb-1">
                            </a>
                        @else
                            <a href="{{ $file->file_url }}" target="_blank" class="d-block">
                                📄 PDF File
                            </a>
                        @endif
                    @empty
                        <span class="text-muted">No files</span>
                    @endforelse
                </td>
                <td>
                    {{-- Mbao deposits are managed from the Mbao dashboard. --}}
                    @if($deposit->status == 'Pending' && !$deposit->isMbao())
                    <div class="pull-right">
                        <a href="{!! Route('delete-unsubmited-deposit', ['id' => $deposit->id, 'status' => 'Inactive']) !!}" 
                           class="btn btn-outline-danger btn-xs"
                           onclick="return confirm('Are you sure you want to delete all inventory data?')">
                           Delete <i class="icofont icofont-ui-delete"></i>
                        </a>
                      </div>
                      <a href='{!! Route('send-approve-deposit', ['id' => $deposit->id]) !!}' class='btn btn-outline-primary btn-xs'>Send To Stock</a>
                    @endif
                </td>

            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $deposits->links() }}

</div>
</div>


{{-- ========================= NEW DEPOSIT MODAL ========================= --}}
<div class="modal fade" id="newModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="{{ route('bank-deposits.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Logistics Bank Deposit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Bank Name</label>
                            <input type="text" name="bank_name" value="CRDB" readonly class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Deposit Amount</label>
                            <input type="number" step="0.01" name="deposited_amount" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Deposit Origin</label>
                            <input type="text" name="deposit_origin" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Deposited Date</label>
                            <input type="date" name="deposited_date" class="form-control">
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label>Upload Slip(s)</label>
                            <input type="file" name="file_path[]" class="form-control" multiple accept="image/*,application/pdf" onchange="handleFiles(this.files)">
                        </div>

                        <div id="previewArea"></div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Save Deposit</button>
                </div>

            </form>

        </div>
    </div>
</div>


</div>
</div>
</div>

@endsection


@push('scripts')
<script>
let selectedFiles = [];

function handleFiles(files) {
    let previewArea = document.getElementById('previewArea');
    previewArea.innerHTML = "";

    selectedFiles = Array.from(files);

    selectedFiles.forEach((file, index) => {
        let box = document.createElement('div');
        box.classList.add('preview-box');

        let removeBtn = document.createElement('button');
        removeBtn.innerHTML = "x";
        removeBtn.classList.add('remove-btn');
        removeBtn.onclick = () => removeFile(index);
        box.appendChild(removeBtn);

        if (file.type.includes("image")) {
            let img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            box.appendChild(img);
        } else {
            let p = document.createElement('p');
            p.innerHTML = "📄 " + file.name;
            p.style.fontWeight = "bold";
            box.appendChild(p);
        }

        previewArea.appendChild(box);
    });
}

function removeFile(index) {
    selectedFiles.splice(index, 1);

    let input = document.querySelector('input[name="file_path[]"]');
    let dt = new DataTransfer();

    selectedFiles.forEach(f => dt.items.add(f));
    input.files = dt.files;

    handleFiles(selectedFiles);
}
</script>
@endpush
