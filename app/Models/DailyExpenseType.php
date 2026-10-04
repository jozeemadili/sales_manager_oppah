<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Expense types for Mbao daily expenses (e.g. umeme, chakula, vibarua).
 * Separate from `expenses`, which holds the types used on inventories.
 */
class DailyExpenseType extends Model
{
	protected $table = 'daily_expense_types';

	protected $casts = [
		'company_id' => 'int',
		'created_by' => 'int',
	];

	protected $fillable = [
		'name',
		'status',
		'company_id',
		'created_by',
	];

	public function expenses()
	{
		return $this->hasMany(DailyExpense::class, 'expense_type_id');
	}
}
