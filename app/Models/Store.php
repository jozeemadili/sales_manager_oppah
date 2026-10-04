<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Store
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
 * @property Company $company
 *
 * @package App\Models
 */
class Store extends Model
{
	protected $table = 'stores';
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

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	// The Mbao dashboard and daily expenses work on this store only (matched by name).
	const MAIN_STORE_NAME = 'MZINGA';

	public static function mainStore()
	{
		return static::where('name', 'like', '%'.self::MAIN_STORE_NAME.'%')->first();
	}
}
