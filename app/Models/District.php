<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class District
 * 
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $region_id
 * 
 * @property Region $region
 * @property Collection|Ward[] $wards
 *
 * @package App\Models
 */
class District extends Model
{
	protected $table = 'districts';
	public $timestamps = false;

	protected $casts = [
		'region_id' => 'int'
	];

	protected $fillable = [
		'name',
		'description',
		'region_id'
	];

	public function region()
	{
		return $this->belongsTo(Region::class);
	}

	public function wards()
	{
		return $this->hasMany(Ward::class);
	}
}
