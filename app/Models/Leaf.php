<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Leaf
 * 
 * @property int $id
 * @property string $emp_id
 * @property string $leave_type
 * @property string|null $leave_details
 * @property int $reg_by
 * @property string $status
 * @property Carbon $reg_date
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property string|null $address
 * @property string|null $email
 * @property string|null $phone_no
 * 
 * @property User $user
 *
 * @package App\Models
 */
class Leaf extends Model
{
	protected $table = 'leaves';
	public $timestamps = false;

	protected $casts = [
		'reg_by' => 'int'
	];

	protected $dates = [
		'reg_date',
		'start_date',
		'end_date'
	];

	protected $fillable = [
		'emp_id',
		'leave_type',
		'leave_details',
		'reg_by',
		'status',
		'reg_date',
		'start_date',
		'end_date',
		'address',
		'email',
		'phone_no'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'reg_by');
	}
}
