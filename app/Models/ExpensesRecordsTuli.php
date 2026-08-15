<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExpensesRecordsTuli
 * 
 * @property int $id
 * @property int $expense_id
 * @property float $amount_used
 * @property string $desr
 * @property string $status
 * @property int $reg_by
 * @property Carbon $reg_at
 * @property int $company_id
 * @property int $inventory_id
 * 
 * @property Company $company
 * @property ExpensesTuli $expenses_tuli
 * @property User $user
 *
 * @package App\Models
 */
class ExpensesRecordsTuli extends Model
{
	protected $table = 'expenses_records_tuli';
	public $timestamps = false;

	protected $casts = [
		'expense_id' => 'int',
		'amount_used' => 'float',
		'reg_by' => 'int',
		'company_id' => 'int',
		'inventory_id' => 'int'
	];

	protected $dates = [
		'reg_at'
	];

	protected $fillable = [
		'expense_id',
		'amount_used',
		'desr',
		'status',
		'reg_by',
		'reg_at',
		'company_id',
		'inventory_id'
	];

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function expenses_tuli()
	{
		return $this->belongsTo(ExpensesTuli::class, 'expense_id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'reg_by');
	}
}
