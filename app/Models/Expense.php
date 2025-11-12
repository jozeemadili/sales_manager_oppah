<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Expense
 * 
 * @property int $id
 * @property string $e_name
 * @property string $status
 * @property string $to_be_used
 * @property Carbon $reg_date
 * @property int $reg_by
 * @property int $company_id
 * 
 * @property User $user
 * @property Company $company
 *
 * @package App\Models
 */
class Expense extends Model
{
	protected $table = 'expenses';
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

	public function user()
	{
		return $this->belongsTo(User::class, 'reg_by');
	}

	public function company()
	{
		return $this->belongsTo(Company::class);
	}
}
