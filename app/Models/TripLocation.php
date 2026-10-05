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
		'tracking' => 'Live tracking (app)',
	];

	const STATUSES = ['ok', 'denied', 'unavailable', 'timeout', 'unsupported'];

	// An open trip tracked by the driver app with no point for this long is
	// flagged "No signal" (the app sends a heartbeat every 10 min when parked).
	const SILENT_MINUTES = 30;

	/**
	 * Open (Pending) trips tracked by the driver app whose last location is
	 * older than SILENT_MINUTES: phone off, GPS off, permission removed or
	 * app closed. Returns [route_id => last seen Carbon].
	 */
	public static function silentTrips()
	{
		$lastSeen = static::query()
			->join('trucks_routes as r', 'r.id', '=', 'trip_locations.route_id')
			->where('r.status', 'Pending')
			->where('trip_locations.source', 'app')
			->groupBy('trip_locations.route_id')
			->selectRaw('trip_locations.route_id, MAX(COALESCE(trip_locations.recorded_at, trip_locations.created_at)) as last_seen')
			->pluck('last_seen', 'route_id');

		$limit = now()->subMinutes(self::SILENT_MINUTES);

		return $lastSeen
			->map(fn ($seen) => \Carbon\Carbon::parse($seen))
			->filter(fn ($seen) => $seen->lt($limit));
	}

	// Why there is no position, in plain words (shown on the trip map pages).
	const REASONS = [
		'denied' => 'Driver refused location',
		'unavailable' => 'Phone could not find location (GPS off?)',
		'timeout' => 'Location took too long',
		'unsupported' => 'Browser has no location support',
		'not_sent' => 'No location sent (old page or script blocked)',
	];

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
		'recorded_at', 'speed_kmh', 'source',
	];

	protected $dates = [
		'recorded_at',
	];

	// When the point was taken (app points can arrive later than they were recorded).
	public function takenAt()
	{
		return $this->recorded_at ?: $this->created_at;
	}

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

	public function reason()
	{
		return self::REASONS[$this->status] ?? $this->status;
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

		$sent = $request->input('geo_status');
		$status = $sent === null ? 'not_sent' : (in_array($sent, self::STATUSES, true) ? $sent : 'unavailable');
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
			'recorded_at' => now(),
			'source' => $request->input('geo_source') === 'app' ? 'app' : 'web',
		]);
	}
}
