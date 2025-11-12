<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Inventory
 * 
 * @property int $id
 * @property string $company_name
 * @property string $vihecle_no
 * @property string $description
 * @property Carbon $inventory_date
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
class Inventory extends Model
{
	protected $table = 'inventories';
	public $timestamps = false;

	protected $casts = [
		'reg_by' => 'int',
		'company_id' => 'int'
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
}
