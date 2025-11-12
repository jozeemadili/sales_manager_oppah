<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Ward
 * 
 * @property int $id
 * @property string $name
 * @property int $district_id
 * 
 * @property District $district
 * @property Collection|Branch[] $branches
 * @property Collection|ClaimNotification[] $claim_notifications
 * @property Collection|Customer[] $customers
 *
 * @package App\Models
 */
class Ward extends Model
{
	protected $table = 'wards';
	public $timestamps = false;

	protected $casts = [
		'district_id' => 'int'
	];

	protected $fillable = [
		'name',
		'district_id'
	];

	public function district()
	{
		return $this->belongsTo(District::class);
	}

	public function branches()
	{
		return $this->hasMany(Branch::class);
	}

	public function claim_notifications()
	{
		return $this->hasMany(ClaimNotification::class);
	}

	public function customers()
	{
		return $this->hasMany(Customer::class);
	}
}
