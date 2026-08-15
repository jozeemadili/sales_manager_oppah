<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomersTuli
 * 
 * @property int $id
 * @property string $name
 * @property string|null $tin
 * @property string|null $vrn
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $physical_addres
 * @property string $status
 * @property Carbon $created_at
 * @property int $created_by
 * @property int $company_id
 * @property int|null $store_id
 * 
 * @property User $user
 * @property Company $company
 * @property StoresTuli|null $stores_tuli
 *
 * @package App\Models
 */
class CustomersTuli extends Model
{
	protected $table = 'customers_tuli';
	public $timestamps = false;

	protected $casts = [
		'created_by' => 'int',
		'company_id' => 'int',
		'store_id' => 'int'
	];

	protected $fillable = [
		'name',
		'tin',
		'vrn',
		'phone',
		'email',
		'physical_addres',
		'status',
		'created_by',
		'company_id',
		'store_id'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function stores_tuli()
	{
		return $this->belongsTo(StoresTuli::class, 'store_id');
	}
}
