<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MyHotel
 * 
 * @property int $id
 * @property string $name
 * @property string $physical_address
 * @property Carbon $reg_at
 * @property int $reg_by
 * @property string $status
 * @property int $company_id
 * 
 * @property User $user
 * @property Company $company
 *
 * @package App\Models
 */
class MyHotel extends Model
{
	protected $table = 'my_hotels';
	public $timestamps = false;

	protected $casts = [
		'reg_by' => 'int',
		'company_id' => 'int'
	];

	protected $dates = [
		'reg_at'
	];

	protected $fillable = [
		'name',
		'physical_address',
		'reg_at',
		'reg_by',
		'status',
		'company_id'
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
