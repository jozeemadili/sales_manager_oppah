<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RoutePlan
 * 
 * @property int $id
 * @property int $route_id
 * @property string $from_location
 * @property string $to_location
 * @property float|null $distance_km
 * @property float|null $fuel_litres
 * @property float $amount_tsh
 * @property string|null $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $created_by
 * 
 * @property TrucksRoute $trucks_route
 * @property User $user
 *
 * @package App\Models
 */
class RoutePlan extends Model
{
	protected $table = 'route_plans';

	protected $casts = [
		'route_id' => 'int',
		'distance_km' => 'float',
		'fuel_litres' => 'float',
		'amount_tsh' => 'float',
		'created_by' => 'int'
	];

	protected $fillable = [
		'route_id',
		'from_location',
		'to_location',
		'distance_km',
		'fuel_litres',
		'amount_tsh',
		'description',
		'created_by'
	];

	public function trucks_route()
	{
		return $this->belongsTo(TrucksRoute::class, 'route_id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}
}
