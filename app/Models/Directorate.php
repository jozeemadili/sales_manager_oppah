<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Directorate
 * 
 * @property int $id
 * @property string $dname
 * @property string $htitle
 * @property string|null $address_details
 * @property int $regby
 * @property string $status
 * @property Carbon $reg_date
 * @property int $company_id
 * 
 * @property User $user
 * @property Company $company
 * @property Collection|Section[] $sections
 *
 * @package App\Models
 */
class Directorate extends Model
{
	protected $table = 'directorates';
	public $timestamps = false;

	protected $casts = [
		'regby' => 'int',
		'company_id' => 'int'
	];

	protected $dates = [
		'reg_date'
	];

	protected $fillable = [
		'dname',
		'htitle',
		'address_details',
		'regby',
		'status',
		'reg_date',
		'company_id'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'regby');
	}

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function sections()
	{
		return $this->hasMany(Section::class, 'directorate');
	}
}
