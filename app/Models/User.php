<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 *
 * @property int $id
 * @property string|null $emp_id
 * @property int|null $branch_id
 * @property int|null $office_location
 * @property string|null $title
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property string|null $type
 * @property int $mobile
 * @property string $id_type
 * @property string|null $id_number
 * @property string|null $email
 * @property Carbon|null $email_verified_at
 * @property bool|null $remember_token
 * @property Carbon|null $dob
 * @property string|null $password
 * @property bool $is_first_time_pin
 * @property string $role
 * @property int|null $company_id
 * @property string|null $status
 * @property Carbon|null $created_at
 * @property int|null $created_by
 * @property int|null $authorized_by
 * @property Carbon|null $authorized_at
 * @property Carbon|null $updated_at
 *
 * @property Company|null $company
 * @property Branch|null $branch
 * @property User|null $user
 * @property Collection|Addon[] $addons
 * @property Collection|Attachment[] $attachments
 * @property Collection|BankAccount[] $bank_accounts
 * @property Collection|Benefit[] $benefits
 * @property Collection|Branch[] $branches
 * @property Collection|ChannelSubscription[] $channel_subscriptions
 * @property Collection|Channel[] $channels
 * @property Collection|ClaimAssessment[] $claim_assessments
 * @property Collection|ClaimDischargeVoucher[] $claim_discharge_vouchers
 * @property Collection|ClaimIntimation[] $claim_intimations
 * @property Collection|ClaimNotification[] $claim_notifications
 * @property Collection|ClaimPayment[] $claim_payments
 * @property Collection|ClaimRejection[] $claim_rejections
 * @property Collection|Company[] $companies
 * @property Collection|Customer[] $customers
 * @property Collection|FinancialServiceProvider[] $financial_service_providers
 * @property Collection|IpfApplication[] $ipf_applications
 * @property Collection|IpfType[] $ipf_types
 * @property Collection|LossNature[] $loss_natures
 * @property Collection|LossType[] $loss_types
 * @property Collection|Payment[] $payments
 * @property Collection|Plan[] $plans
 * @property Collection|Policy[] $policies
 * @property Collection|Pricing[] $pricings
 * @property Collection|Product[] $products
 * @property Collection|QuotationsAddon[] $quotations_addons
 * @property Collection|Risk[] $risks
 * @property Collection|User[] $users
 * @property Collection|Vehicle[] $vehicles
 *
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

	protected $table = 'users';

	protected $casts = [
		'mobile' => 'int',
		'remember_token' => 'bool',
		'is_first_time_pin' => 'bool',
		'company_id' => 'int',
		'created_by' => 'int',
		'authorized_by' => 'int',
		'office_location' => 'int'
	];

	protected $dates = [
		'email_verified_at',
		'dob',
		'authorized_at'
	];

	protected $hidden = [
		'remember_token',
		'password'
	];

	protected $fillable = [
		'emp_id',
		'branch_id',
		'title',
		'first_name',
		'middle_name',
		'last_name',
		'type',
		'mobile',
		'id_type',
		'id_number',
		'email',
		'email_verified_at',
		'remember_token',
		'dob',
		'password',
		'is_first_time_pin',
		'role',
		'company_id',
		'status',
		'created_by',
		'authorized_by',
		'authorized_at',
		'office_location'
	];

	const ROLE_SUPER_ADMIN = 'SUPER_ADMIN';
	const ROLE_ADMIN = 'ADMIN';
	const ROLE_MBAO = 'Mbao';
	const ROLE_HARDWARE = 'Hardware';
	const ROLE_DRIVER = 'Driver';

	public function isSuperAdmin()
	{
		return $this->role === self::ROLE_SUPER_ADMIN;
	}

	public function hasFullAccess()
	{
		return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN]);
	}

	public function assignableRoles()
	{
		if ($this->isSuperAdmin()) {
			return [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN, self::ROLE_MBAO, self::ROLE_HARDWARE, self::ROLE_DRIVER];
		}

		if ($this->hasFullAccess()) {
			return [self::ROLE_MBAO, self::ROLE_HARDWARE, self::ROLE_DRIVER];
		}

		return [];
	}

	// Whether this user may change another user's role/status. Nobody manages
	// their own account here (avoids self-lockout); ADMINs only manage the
	// roles they could assign, SUPER_ADMINs manage everyone else.
	public function canManage(User $target)
	{
		if ($this->id === $target->id || !$this->hasFullAccess()) {
			return false;
		}

		return $this->isSuperAdmin() || in_array($target->role, $this->assignableRoles());
	}

	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function branch()
	{
		return $this->belongsTo(Branch::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'authorized_by');
	}

	public function addons()
	{
		return $this->hasMany(Addon::class, 'created_by');
	}

	public function attachments()
	{
		return $this->hasMany(Attachment::class, 'created_by');
	}

	public function bank_accounts()
	{
		return $this->hasMany(BankAccount::class, 'authorized_by');
	}

	public function benefits()
	{
		return $this->hasMany(Benefit::class, 'authorized_by');
	}

	public function branches()
	{
		return $this->hasMany(Branch::class, 'created_by');
	}

	public function channel_subscriptions()
	{
		return $this->hasMany(ChannelSubscription::class, 'created_by');
	}

	public function channels()
	{
		return $this->hasMany(Channel::class, 'authorized_by');
	}

	public function claim_assessments()
	{
		return $this->hasMany(ClaimAssessment::class, 'authorized_by');
	}

	public function claim_discharge_vouchers()
	{
		return $this->hasMany(ClaimDischargeVoucher::class, 'authorized_by');
	}

	public function claim_intimations()
	{
		return $this->hasMany(ClaimIntimation::class, 'authorized_by');
	}

	public function claim_notifications()
	{
		return $this->hasMany(ClaimNotification::class, 'authorized_by');
	}

	public function claim_payments()
	{
		return $this->hasMany(ClaimPayment::class, 'created_by');
	}

	public function claim_rejections()
	{
		return $this->hasMany(ClaimRejection::class, 'authorized_by');
	}

	public function companies()
	{
		return $this->hasMany(Company::class, 'created_by');
	}

	public function customers()
	{
		return $this->hasMany(Customer::class, 'authorized_by');
	}

	public function financial_service_providers()
	{
		return $this->hasMany(FinancialServiceProvider::class, 'authorized_by');
	}

	public function ipf_applications()
	{
		return $this->hasMany(IpfApplication::class, 'rejected_by');
	}

	public function ipf_types()
	{
		return $this->hasMany(IpfType::class, 'created_by');
	}

	public function loss_natures()
	{
		return $this->hasMany(LossNature::class, 'created_by');
	}

	public function loss_types()
	{
		return $this->hasMany(LossType::class, 'authorized_by');
	}

	public function payments()
	{
		return $this->hasMany(Payment::class, 'created_by');
	}

	public function plans()
	{
		return $this->hasMany(Plan::class, 'created_by');
	}

	public function policies()
	{
		return $this->hasMany(Policy::class, 'created_by');
	}

	public function pricings()
	{
		return $this->hasMany(Pricing::class, 'authorized_by');
	}

	public function products()
	{
		return $this->hasMany(Product::class, 'created_by');
	}

	public function quotations_addons()
	{
		return $this->hasMany(QuotationsAddon::class, 'created_by');
	}

	public function quotations()
	{
		return $this->hasMany(Quotation::class, 'created_by');
	}

	public function risks()
	{
		return $this->hasMany(Risk::class, 'created_by');
	}

	public function users()
	{
		return $this->hasMany(User::class, 'authorized_by');
	}

	public function vehicles()
	{
		return $this->hasMany(Vehicle::class, 'created_by');
	}
}
