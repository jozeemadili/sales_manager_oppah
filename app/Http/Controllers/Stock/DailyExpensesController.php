<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\DailyExpense;
use App\Models\DailyExpenseType;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Mbao daily (running) expenses — separate from inventory expenses.
 */
class DailyExpensesController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            abort_unless($user->role === 'Mbao' || $user->hasFullAccess(), 403);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : Carbon::now()->startOfMonth();
        $to = $request->filled('to') ? Carbon::parse($request->to)->endOfDay() : Carbon::now()->endOfDay();
        $store = $this->mainStore();
        $storeId = $store->id;

        $query = DailyExpense::active()
            ->with(['type', 'store', 'user'])
            ->where('company_id', $user->company_id)
            ->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])
            ->when($storeId !== 'all', fn ($q) => $q->where('store_id', $storeId));

        $total = (clone $query)->sum('amount');

        // One card per day in the period; clicking a card lists that day's entries.
        $days = (clone $query)
            ->orderByDesc('expense_date')
            ->orderBy('created_at')
            ->get()
            ->groupBy(fn ($expense) => $expense->expense_date->toDateString());

        $expenses = $query->orderByDesc('expense_date')->orderByDesc('id')->paginate(20)->withQueryString();

        return view('admin.sales_management.daily-expenses', [
            'expenses' => $expenses,
            'days' => $days,
            'total' => $total,
            'types' => DailyExpenseType::where('company_id', $user->company_id)->where('status', 'Active')->orderBy('name')->get(),
            'store' => $store,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'storeId' => $storeId,
            'minDate' => $this->minDate()->toDateString(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        DailyExpense::create($data + [
            'company_id' => Auth::user()->company_id,
            'recorded_by' => Auth::user()->id,
            'status' => 'Active',
        ]);

        return back()->with('success', 'Daily expense recorded successfully.');
    }

    public function update(Request $request, $id)
    {
        $expense = $this->findChangeable($id);

        $expense->update($this->validated($request));

        return back()->with('success', 'Daily expense updated successfully.');
    }

    public function destroy($id)
    {
        $expense = $this->findChangeable($id);

        // Soft delete: keep the row for the audit trail.
        $expense->update(['status' => 'Deleted']);

        return back()->with('success', 'Daily expense deleted.');
    }

    public function storeType(Request $request)
    {
        $request->validate(['name' => ['required', 'string', 'max:200']]);

        DailyExpenseType::firstOrCreate(
            ['name' => trim($request->name), 'company_id' => Auth::user()->company_id],
            ['status' => 'Active', 'created_by' => Auth::user()->id]
        );

        return back()->with('success', 'Expense type <b>'.e(strtoupper($request->name)).'</b> added.');
    }

    private function validated(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'expense_type_id' => ['required', 'integer', 'exists:daily_expense_types,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'expense_date' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:'.$this->minDate()->toDateString()],
            'description' => ['nullable', 'string', 'max:500'],
        ], [
            'expense_date.after_or_equal' => 'You can only record expenses for the last '.DailyExpense::BACKDATE_DAYS.' days.',
            'expense_date.before_or_equal' => 'The expense date cannot be in the future.',
        ]);

        // Daily expenses are always recorded against the main (Mzinga) store.
        $data['store_id'] = $this->mainStore()->id;

        return $data;
    }

    private function mainStore()
    {
        $store = Store::mainStore();
        abort_unless($store, 500, 'Main store ('.Store::MAIN_STORE_NAME.') not found.');

        return $store;
    }

    private function minDate()
    {
        return Auth::user()->hasFullAccess()
            ? Carbon::create(2000, 1, 1)
            : Carbon::today()->subDays(DailyExpense::BACKDATE_DAYS);
    }

    private function findChangeable($id)
    {
        $expense = DailyExpense::active()
            ->where('company_id', Auth::user()->company_id)
            ->findOrFail($id);

        abort_unless($expense->canBeChangedBy(Auth::user()), 403, 'You can only change your own entries on the day you recorded them.');

        return $expense;
    }
}
