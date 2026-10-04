<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Remembers entries the driver app has already sent (by the app's own uuid),
 * so a retry after a dropped connection returns the saved record instead of
 * creating a duplicate.
 */
class AppSyncRequest extends Model
{
	protected $table = 'app_sync_requests';
	public $timestamps = false;

	protected $fillable = ['user_id', 'uuid', 'type', 'ref_id', 'created_at'];

	public static function find_ref($userId, $uuid, $type)
	{
		if (!$uuid) {
			return null;
		}

		return static::where('user_id', $userId)->where('uuid', $uuid)->where('type', $type)->value('ref_id');
	}

	public static function remember($userId, $uuid, $type, $refId)
	{
		if ($uuid) {
			static::firstOrCreate(['user_id' => $userId, 'uuid' => $uuid], ['type' => $type, 'ref_id' => $refId, 'created_at' => now()]);
		}
	}
}
