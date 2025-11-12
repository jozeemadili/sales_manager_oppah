<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Section
 * 
 * @property int $id
 * @property string $sname
 * @property string $htitle
 * @property int $directorate
 * @property string|null $address_details
 * @property int $regby
 * @property string $status
 * @property Carbon $reg_date
 * @property int $company_id
 * 
 * @property User $user
 * @property Company $company
 *
 * @package App\Models
 */
class Section extends Model
{
	protected $table = 'sections';
	public $timestamps = false;

	protected $casts = [
		'directorate' => 'int',
		'regby' => 'int',
		'company_id' => 'int'
	];

	protected $dates = [
		'reg_date'
	];

	protected $fillable = [
		'sname',
		'htitle',
		'directorate',
		'address_details',
		'regby',
		'status',
		'reg_date',
		'company_id'
	];

	public function directorate()
	{
		return $this->belongsTo(Directorate::class, 'directorate');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'regby');
	}

	public function company()
	{
		return $this->belongsTo(Company::class);
	}
}
