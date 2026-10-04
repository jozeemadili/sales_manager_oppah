<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Where the driver was when they saved something on a trip. Captured by the
 * browser (resources/views/admin/sales_management/partials/geo-capture.blade.php)
 * and sent as geo_* fields. Saving is never blocked: without a position the
 * row keeps the reason in `status` ("no location").
 */
class TripLocation extends Model
{
	protected $table = 'trip_locations';

	const EVENTS = [
		'trip_created' => 'Trip created',
		'route_plan' => 'Route plan / fuel',
		'expense' => 'Trip expense',
		'submitted' => 'Trip submitted',
	];

	const STATUSES = ['ok', 'denied', 'unavailable', 'timeout', 'unsupported'];

	protected $casts = [
		'route_id' => 'int',
		'ref_id' => 'int',
		'latitude' => 'float',
		'longitude' => 'float',
		'accuracy_m' => 'int',
		'recorded_by' => 'int',
	];

	protected $fillable = [
		'route_id', 'event', 'ref_id', 'latitude', 'longitude', 'accuracy_m', 'status', 'recorded_by',
	];

	public function route()
	{
		return $this->belongsTo(TrucksRoute::class, 'route_id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'recorded_by');
	}

	public function hasPosition()
	{
		return $this->status === 'ok' && $this->latitude !== null && $this->longitude !== null;
	}

	public function eventLabel()
	{
		return self::EVENTS[$this->event] ?? $this->event;
	}

	/**
	 * Store the geo_* fields sent with a trip form. Anything missing or out
	 * of range is recorded as "no location" rather than rejected.
	 */
	public static function fromRequest(Request $request, $routeId, $event, $refId = null)
	{
		if (!$routeId || !array_key_exists($event, self::EVENTS)) {
			return null;
		}

		$status = in_array($request->input('geo_status'), self::STATUSES, true) ? $request->input('geo_status') : 'unavailable';
		$lat = $request->input('geo_lat');
		$lng = $request->input('geo_lng');
		$valid = $status === 'ok' && is_numeric($lat) && is_numeric($lng)
			&& abs($lat) <= 90 && abs($lng) <= 180 && !($lat == 0 && $lng == 0);

		return static::create([
			'route_id' => (int) $routeId,
			'event' => $event,
			'ref_id' => $refId,
			'latitude' => $valid ? round((float) $lat, 7) : null,
			'longitude' => $valid ? round((float) $lng, 7) : null,
			'accuracy_m' => $valid && is_numeric($request->input('geo_accuracy')) ? (int) $request->input('geo_accuracy') : null,
			'status' => $valid ? 'ok' : ($status === 'ok' ? 'unavailable' : $status),
			'recorded_by' => Auth::id(),
		]);
	}
}
