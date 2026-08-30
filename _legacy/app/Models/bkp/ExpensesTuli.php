<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExpensesTuli
 * 
 * @property int $id
 * @property string $e_name
 * @property string $status
 * @property Carbon $reg_date
 * @property int $reg_by
 * @property int $company_id
 * @property string|null $to_be_used
 * 
 * @property Company $company
 * @property User $user
 *
 * @package App\Models
 */
class ExpensesTuli extends Model
{
	protected $table = 'expenses_tuli';
	public $timestamps = false;

	protected $casts = [
		'reg_by' => 'int',
		'company_id' => 'int'
	];

	protected $dates = [
		'reg_date'
	];

	protected $fillable = [
		'e_name',
		'status',
		'reg_date',
		'reg_by',
		'company_id',
		'to_be_used'
	];

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'reg_by');
	}
}
