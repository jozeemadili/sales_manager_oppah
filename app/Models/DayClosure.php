<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Mbao "End Day". After closing, Mbao users are view-only until
 * locked_until (the next 06:00 — "saa 12 asubuhi"); admins are never locked
 * and can re-open a closed day. The snapshot keeps the figures as they were
 * at closing so the PDF can be re-printed later.
 */
class DayClosure extends Model
{
	protected $table = 'day_closures';

	// Mbao re-opens automatically at this hour (06:00 = saa 12 asubuhi).
	const REOPEN_HOUR = 6;

	protected $casts = [
		'store_id' => 'int',
		'closed_by' => 'int',
		'reopened_by' => 'int',
		'snapshot' => 'array',
	];

	protected $dates = [
		'business_date',
		'closed_at',
		'locked_until',
		'reopened_at',
	];

	protected $fillable = [
		'store_id',
		'business_date',
		'closed_by',
		'closed_at',
		'locked_until',
		'status',
		'reopened_by',
		'reopened_at',
		'snapshot',
	];

	public function closer()
	{
		return $this->belongsTo(User::class, 'closed_by');
	}

	public function reopener()
	{
		return $this->belongsTo(User::class, 'reopened_by');
	}

	// Next 06:00 after the given moment.
	public static function lockUntil(Carbon $closedAt)
	{
		$reopen = $closedAt->copy()->setTime(self::REOPEN_HOUR, 0);

		return $reopen->gt($closedAt) ? $reopen : $reopen->addDay();
	}

	// The closing currently locking this store, if any.
	public static function activeLock($storeId)
	{
		if (!$storeId) {
			return null;
		}

		return static::where('store_id', $storeId)
			->where('status', 'Closed')
			->where('locked_until', '>', Carbon::now())
			->latest('id')
			->first();
	}

	// Mbao (main store) lock that applies to this user — admins are never locked.
	public static function lockFor(?User $user)
	{
		if (!$user || $user->hasFullAccess()) {
			return null;
		}

		return static::activeLock(optional(Store::mainStore())->id);
	}
}
