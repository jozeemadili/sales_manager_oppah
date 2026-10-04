<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * A Mbao day-to-day running cost recorded for a store on a given date.
 * Not tied to an inventory (inventory expenses live in `expenses_records`).
 */
class DailyExpense extends Model
{
	protected $table = 'daily_expenses';

	// Non-admins may record up to this many days back.
	const BACKDATE_DAYS = 7;

	protected $casts = [
		'expense_type_id' => 'int',
		'amount' => 'float',
		'store_id' => 'int',
		'company_id' => 'int',
		'recorded_by' => 'int',
	];

	protected $dates = [
		'expense_date',
	];

	protected $fillable = [
		'expense_type_id',
		'amount',
		'expense_date',
		'store_id',
		'description',
		'company_id',
		'recorded_by',
		'status',
	];

	public function type()
	{
		return $this->belongsTo(DailyExpenseType::class, 'expense_type_id');
	}

	public function store()
	{
		return $this->belongsTo(Store::class, 'store_id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'recorded_by');
	}

	public function scopeActive($query)
	{
		return $query->where('status', 'Active');
	}

	// Admins can always change an entry; others only their own, on the day it was entered.
	public function canBeChangedBy(User $user)
	{
		if ($user->hasFullAccess()) {
			return true;
		}

		return $this->recorded_by === $user->id
			&& Carbon::parse($this->created_at)->isToday();
	}
}
