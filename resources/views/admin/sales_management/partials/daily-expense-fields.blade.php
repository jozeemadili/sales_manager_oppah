<div class="row">
    <div class="col-lg-4">
        <div class="form-group">
            <label class="col-form-label">Date</label>
            <input class="form-control" type="date" name="expense_date" required
                   min="{{ $minDate }}" max="{{ now()->toDateString() }}"
                   value="{{ $expense ? $expense->expense_date->toDateString() : now()->toDateString() }}">
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <label class="col-form-label">Expense Type</label>
            <select class="form-select" name="expense_type_id" required>
                <option value="">--- Choose ---</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" @selected($expense && $expense->expense_type_id === $type->id)>{{ strtoupper($type->name) }}</option>
                @endforeach
            </select>
            @if($types->isEmpty())
                <small class="text-danger">Add an expense type first (Expense Types button).</small>
            @endif
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <label class="col-form-label">Amount (TZS)</label>
            <input class="form-control" type="number" name="amount" required min="1" step="any"
                   value="{{ $expense ? $expense->amount : '' }}">
        </div>
    </div>
    <div class="col-lg-4">
        <div class="form-group">
            <label class="col-form-label">Store</label>
            <input class="form-control" type="text" value="{{ strtoupper($store->name) }}" readonly>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="form-group">
            <label class="col-form-label">Description</label>
            <input class="form-control" type="text" name="description" maxlength="500"
                   value="{{ $expense ? $expense->description : '' }}" placeholder="Optional">
        </div>
    </div>
</div>
