<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InventoriesTuli
 * 
 * @property int $id
 * @property string $company_name
 * @property string|null $vihecle_no
 * @property string|null $description
 * @property Carbon $inventory_date
 * @property int $reg_by
 * @property Carbon $reg_at
 * @property string $status
 * @property int $company_id
 * @property int|null $store_id
 * 
 * @property Company $company
 * @property User $user
 * @property StoresTuli|null $stores_tuli
 *
 * @package App\Models
 */
class InventoriesTuli extends Model
{
	protected $table = 'inventories_tuli';
	public $timestamps = false;

	protected $casts = [
		'reg_by' => 'int',
		'company_id' => 'int',
		'store_id' => 'int'
	];

	protected $dates = [
		'inventory_date',
		'reg_at'
	];

	protected $fillable = [
		'company_name',
		'vihecle_no',
		'description',
		'inventory_date',
		'reg_by',
		'reg_at',
		'status',
		'company_id',
		'store_id'
	];

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'reg_by');
	}

	public function stores_tuli()
	{
		return $this->belongsTo(StoresTuli::class, 'store_id');
	}
}
