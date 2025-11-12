<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HotelCustomer
 * 
 * @property int $id
 * @property string $name
 * @property int $mobile
 * @property string $email
 * @property string $tribe
 * @property string $occupation
 * @property string $status
 * @property string $physical_addres
 * @property int $created_by
 * @property Carbon $created_at
 * @property int $company_id
 * 
 * @property Company $company
 * @property User $user
 *
 * @package App\Models
 */
class HotelCustomer extends Model
{
	protected $table = 'hotel_customers';
	public $timestamps = false;

	protected $casts = [
		'mobile' => 'int',
		'created_by' => 'int',
		'company_id' => 'int'
	];

	protected $fillable = [
		'name',
		'mobile',
		'email',
		'tribe',
		'occupation',
		'status',
		'created_by',
		'company_id',
		'physical_addres',
		'created_at'
	];

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}
}
