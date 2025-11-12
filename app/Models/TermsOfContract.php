<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TermsOfContract
 * 
 * @property int $id
 * @property int $emp_id
 * @property string $term_of_contracts
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property string $status
 * @property string $reg_by
 * @property Carbon $reg_date
 * @property string $duration
 * 
 * @property Employee $employee
 *
 * @package App\Models
 */
class TermsOfContract extends Model
{
	protected $table = 'terms_of_contracts';
	public $timestamps = false;

	protected $casts = [
		'emp_id' => 'int'
	];

	protected $dates = [
		'start_date',
		'end_date',
		'reg_date'
	];

	protected $fillable = [
		'emp_id',
		'term_of_contracts',
		'start_date',
		'end_date',
		'status',
		'reg_by',
		'reg_date',
		'duration'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'emp_id');
	}
}
