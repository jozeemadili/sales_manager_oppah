<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StoresTuli
 * 
 * @property int $id
 * @property string $name
 * @property string $physica_addres
 * @property int $reg_by
 * @property Carbon $reg_at
 * @property string $status
 * @property int $company_id
 * 
 * @property User $user
 *
 * @package App\Models
 */
class StoresTuli extends Model
{
	protected $table = 'stores_tuli';
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
		'physica_addres',
		'reg_by',
		'reg_at',
		'status',
		'company_id'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'reg_by');
	}
}
