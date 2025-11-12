<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Employee
 * 
 * @property int $id
 * @property string $emp_id
 * @property string|null $fname
 * @property string|null $mname
 * @property string|null $lname
 * @property string $email
 * @property Carbon|null $first_appointmen_date
 * @property Carbon|null $confirmation_date
 * @property Carbon|null $retire_date
 * @property Carbon|null $dob
 * @property string|null $gender
 * @property string|null $tribe
 * @property string|null $religion
 * @property int|null $domicile_place
 * @property string|null $nationality
 * @property string|null $nationality_type
 * @property int|null $directorate
 * @property int|null $section
 * @property int|null $branch
 * @property string|null $terms_of_service
 * @property string|null $marital_status
 * @property string|null $education_level
 * @property string|null $qualification
 * @property float|null $salary
 * @property string|null $status
 * @property string|null $reg_by
 * @property Carbon $reg_date
 * @property string|null $pension_fund
 * @property int $company_id
 * 
 * @property Company $company
 * @property Ward|null $ward
 * @property Collection|TermsOfContract[] $terms_of_contracts
 *
 * @package App\Models
 */
class Employee extends Model
{
	protected $table = 'employees';
	public $timestamps = false;

	protected $casts = [
		'domicile_place' => 'int',
		'directorate' => 'int',
		'section' => 'int',
		'branch' => 'int',
		'salary' => 'float',
		'company_id' => 'int'
	];

	protected $dates = [
		'first_appointmen_date',
		'confirmation_date',
		'retire_date',
		'dob',
		'reg_date'
	];

	protected $fillable = [
		'emp_id',
		'fname',
		'mname',
		'lname',
		'email',
		'first_appointmen_date',
		'confirmation_date',
		'retire_date',
		'dob',
		'gender',
		'tribe',
		'religion',
		'domicile_place',
		'nationality',
		'nationality_type',
		'directorate',
		'section',
		'branch',
		'terms_of_service',
		'marital_status',
		'education_level',
		'qualification',
		'salary',
		'status',
		'reg_by',
		'reg_date',
		'pension_fund',
		'company_id'
	];

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function directorate()
	{
		return $this->belongsTo(Directorate::class, 'directorate');
	}

	public function section()
	{
		return $this->belongsTo(Section::class, 'section');
	}

	public function ward()
	{
		return $this->belongsTo(Ward::class, 'domicile_place');
	}

	public function branch()
	{
		return $this->belongsTo(Branch::class, 'branch');
	}

	public function terms_of_contracts()
	{
		return $this->hasMany(TermsOfContract::class, 'emp_id');
	}
}
