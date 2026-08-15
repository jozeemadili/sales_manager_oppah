<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TrucksRoute
 * 
 * @property int $id
 * @property Carbon $route_date
 * @property string $trip_no
 * @property string $status
 * @property string $going_customer
 * @property string|null $return_customer
 * @property float|null $going_transport_fee
 * @property float|null $return_transport_fee
 * @property float|null $total_fee
 * @property int $created_by
 * @property Carbon|null $created_date
 * @property int $truck_id
 * 
 * @property OurTruck $our_truck
 * @property User $user
 *
 * @package App\Models
 */
class TrucksRoute extends Model
{
	protected $table = 'trucks_routes';
	public $timestamps = false;

	protected $casts = [
		'going_transport_fee' => 'float',
		'return_transport_fee' => 'float',
		'total_fee' => 'float',
		'created_by' => 'int',
		'truck_id' => 'int'
	];

	protected $dates = [
		'route_date',
		'created_date'
	];

	protected $fillable = [
		'route_date',
		'trip_no',
		'going_customer',
		'return_customer',
		'going_transport_fee',
		'return_transport_fee',
		'total_fee',
		'created_by',
		'created_date',
		'truck_id',
		'status'
	];

	public function our_truck()
	{
		return $this->belongsTo(OurTruck::class, 'truck_id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function routePlans()
	{
		return $this->hasMany(RoutePlan::class, 'route_id');
	}

	public function expensesRecords()
	{
		return $this->hasMany(ExpensesRecordsTruck::class, 'route_id');
	}
}
