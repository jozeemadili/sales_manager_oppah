<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExpensesRecordsTruck
 * 
 * @property int $id
 * @property int $expense_id
 * @property float $amount_used
 * @property string $desr
 * @property string $status
 * @property int $reg_by
 * @property Carbon $reg_at
 * @property int $company_id
 * @property int $route_id
 * 
 * @property TrucksRoute $trucks_route
 * @property User $user
 * @property Expense $expense
 *
 * @package App\Models
 */
class ExpensesRecordsTruck extends Model
{
	protected $table = 'expenses_records_trucks';
	public $timestamps = false;

	protected $casts = [
		'expense_id' => 'int',
		'amount_used' => 'float',
		'reg_by' => 'int',
		'company_id' => 'int',
		'route_id' => 'int'
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
		'route_id'
	];

	public function trucks_route()
	{
		return $this->belongsTo(TrucksRoute::class, 'route_id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'reg_by');
	}

	public function expense()
	{
		return $this->belongsTo(Expense::class);
	}
}
