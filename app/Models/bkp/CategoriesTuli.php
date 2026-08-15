<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CategoriesTuli
 * 
 * @property int $id
 * @property string $name
 * @property string $descr
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
class CategoriesTuli extends Model
{
	protected $table = 'categories_tuli';
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
		'descr',
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
