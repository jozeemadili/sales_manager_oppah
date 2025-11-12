<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExpensesRecord
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
 * @property User $user
 * @property Expense $expense
 * @property Inventory $inventory
 *
 * @package App\Models
 */
class ExpensesRecord extends Model
{
	protected $table = 'expenses_records';
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

	public function user()
	{
		return $this->belongsTo(User::class, 'reg_by');
	}

	public function expense()
	{
		return $this->belongsTo(Expense::class);
	}

	public function inventory()
	{
		return $this->belongsTo(Inventory::class);
	}
}
