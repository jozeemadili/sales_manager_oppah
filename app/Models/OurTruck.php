<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OurTruck
 * 
 * @property int $id
 * @property string $plate_no
 * @property int $driver_id
 * @property Carbon $created_at
 * @property int $created_by
 * 
 * @property User $user
 *
 * @package App\Models
 */
class OurTruck extends Model
{
	protected $table = 'our_trucks';
	public $timestamps = false;

	protected $casts = [
		'driver_id' => 'int',
		'created_by' => 'int'
	];

	protected $fillable = [
		'plate_no',
		'driver_id',
		'created_by'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}
}
