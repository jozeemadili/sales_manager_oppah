<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Company
 * 
 * @property int $id
 * @property string $name
 * @property string|null $short_form
 * @property string $registration_number
 * @property string $license_number
 * @property string|null $tin
 * @property string $code
 * @property string|null $sale_point_code
 * @property string $category
 * @property string|null $logo
 * @property string|null $email_address
 * @property string|null $postal_address
 * @property string|null $color
 * 
 * @property int|null $phone_number
 * @property string|null $contact_person
 * @property int $created_by
 * @property Carbon $create_at
 * @property int|null $authorized_by
 * @property Carbon|null $authorized_at
 * @property string $status
 * 
 * @property User $user
 * 
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class Company extends Model
{
	protected $table = 'companies';
	public $timestamps = false;

	protected $casts = [
		'phone_number' => 'int',
		'created_by' => 'int',
		'authorized_by' => 'int'
	];

	protected $dates = [
		'create_at',
		'authorized_at'
	];

	protected $fillable = [
		'name',
		'short_form',
		'registration_number',
		'license_number',
		'tin',
		'code',
		'sale_point_code',
		'category',
		'logo',
		'color',
		'email_address',
		'postal_address',
		'phone_number',
		'contact_person',
		'created_by',
		'create_at',
		'authorized_by',
		'authorized_at',
		'status'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	

	public function branches()
	{
		return $this->hasMany(Branch::class);
	}

	

	public function users()
	{
		return $this->hasMany(User::class);
	}
}
