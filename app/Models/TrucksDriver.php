<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TrucksDriver
 * 
 * @property int $id
 * @property int $driver_id
 * @property int $car_id
 * @property string $status
 * @property int $created_by
 * 
 * @property OurTruck $our_truck
 * @property User $user
 *
 * @package App\Models
 */
class TrucksDriver extends Model
{
	protected $table = 'trucks_drivers';
	public $timestamps = false;

	protected $casts = [
		'driver_id' => 'int',
		'car_id' => 'int',
		'created_by' => 'int'
	];

	protected $fillable = [
		'driver_id',
		'car_id',
		'status',
		'created_by'
	];

	public function our_truck()
	{
		return $this->belongsTo(OurTruck::class, 'car_id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'driver_id');
	}
}
