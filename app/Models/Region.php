<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Region
 * 
 * @property int $id
 * @property string $name
 * @property string $code
 * 
 * @property Collection|District[] $districts
 *
 * @package App\Models
 */
class Region extends Model
{
	protected $table = 'regions';
	public $timestamps = false;

	protected $fillable = [
		'name',
		'code'
	];

	public function districts()
	{
		return $this->hasMany(District::class);
	}
}
