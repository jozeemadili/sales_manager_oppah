import 'dart:async';

import 'package:flutter/foundation.dart';
import 'package:geolocator/geolocator.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'config.dart';
import 'local_db.dart';
import 'location_helper.dart';

/// Background tracking of the driver's active trip. Uses an Android
/// foreground service (the "Tracking trip" notification) so it keeps running
/// with the app in the background or the screen off. Points are stored on
/// the phone and sent by SyncService, so they survive no-network stretches.
///
/// Drivers cannot switch it off: the newest open (Pending) trip is tracked
/// automatically, and tracking stops only when that trip is sent to stock.
/// A heartbeat point is also recorded every [Config.heartbeatInterval] while
/// parked, so the office can tell "parked" from "phone off / GPS off".
class Tracker {
  static final ValueNotifier<String?> activeTrip = ValueNotifier<String?>(null);
  static final ValueNotifier<String> activeLabel = ValueNotifier<String>('');

  /// Why tracking is not running when it should be (null when fine).
  static final ValueNotifier<String?> problem = ValueNotifier<String?>(null);

  static StreamSubscription<Position>? _sub;
  static Timer? _heartbeat;

  /// Resume tracking after the app was restarted.
  static Future<void> resume() async {
    final prefs = await SharedPreferences.getInstance();
    final ref = prefs.getString('tracking_trip');
    if (ref != null) await start(ref, prefs.getString('tracking_label') ?? '');
  }

  /// Track the newest open trip. [openTrips] are refs ("uuid:..." / "id:...")
  /// newest first, with their labels. Stops when there is no open trip.
  static Future<void> follow(List<MapEntry<String, String>> openTrips) async {
    if (openTrips.isEmpty) {
      if (activeTrip.value != null) await stop();
      return;
    }

    // Already tracking one of the open trips (possibly under its other ref)?
    final active = activeTrip.value;
    if (active != null && _sub != null) {
      final activeId = await LocalDb.resolveTrip(active);
      for (final t in openTrips) {
        if (t.key == active || (activeId != null && await LocalDb.resolveTrip(t.key) == activeId)) {
          if (t.value.isNotEmpty && activeLabel.value != t.value) activeLabel.value = t.value;
          return;
        }
      }
    }

    await start(openTrips.first.key, openTrips.first.value);
  }

  static Future<bool> start(String tripRef, String label) async {
    final why = await LocationHelper.problem();
    if (why != null) {
      problem.value = why;
      return false;
    }
    problem.value = null;

    await _sub?.cancel();
    _heartbeat?.cancel();

    final settings = AndroidSettings(
      accuracy: LocationAccuracy.high,
      distanceFilter: Config.trackingDistanceMeters,
      intervalDuration: Config.trackingInterval,
      foregroundNotificationConfig: ForegroundNotificationConfig(
        notificationTitle: 'Oppah - trip in progress',
        notificationText: label.isEmpty ? 'Your location is recorded for this trip.' : 'Trip $label: your location is recorded.',
        enableWakeLock: true,
        setOngoing: true,
      ),
    );

    _sub = Geolocator.getPositionStream(locationSettings: settings).listen(
      (pos) => _save(tripRef, pos),
      onError: (_) async => problem.value = await LocationHelper.problem() ?? 'Location stopped. / Mahali pamesimama.',
    );

    // Heartbeat while parked (no movement = no stream updates).
    _heartbeat = Timer.periodic(Config.heartbeatInterval, (_) async {
      final why = await LocationHelper.problem();
      problem.value = why;
      if (why != null) return;
      try {
        final pos = await Geolocator.getCurrentPosition(
          locationSettings: const LocationSettings(accuracy: LocationAccuracy.high, timeLimit: Duration(seconds: 30)),
        );
        _save(tripRef, pos);
      } catch (_) {
        final last = await Geolocator.getLastKnownPosition();
        if (last != null) _save(tripRef, last, at: DateTime.now());
      }
    });

    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('tracking_trip', tripRef);
    await prefs.setString('tracking_label', label);
    activeTrip.value = tripRef;
    activeLabel.value = label;
    return true;
  }

  static void _save(String tripRef, Position pos, {DateTime? at}) {
    LocalDb.addPoint(tripRef, pos.latitude, pos.longitude, pos.accuracy, pos.speed < 0 ? 0 : pos.speed, at ?? pos.timestamp);
  }

  /// Only called when the trip is sent to stock (or on logout).
  static Future<void> stop() async {
    await _sub?.cancel();
    _sub = null;
    _heartbeat?.cancel();
    _heartbeat = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('tracking_trip');
    await prefs.remove('tracking_label');
    activeTrip.value = null;
    activeLabel.value = '';
    problem.value = null;
  }

  /// True when [tripRef] (or the same trip under its other reference) is tracked.
  static bool isTracking(String tripRef, [String? otherRef]) =>
      activeTrip.value != null && (activeTrip.value == tripRef || activeTrip.value == otherRef);
}
