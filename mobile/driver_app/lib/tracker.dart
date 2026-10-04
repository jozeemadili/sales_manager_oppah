import 'dart:async';

import 'package:flutter/foundation.dart';
import 'package:geolocator/geolocator.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'config.dart';
import 'local_db.dart';
import 'location_helper.dart';

/// Background tracking while a trip is active. Uses an Android foreground
/// service (the "Tracking trip" notification) so it keeps running when the
/// app is in the background or the screen is off. Points are stored on the
/// phone and sent by SyncService, so they survive no-network stretches.
class Tracker {
  static final ValueNotifier<String?> activeTrip = ValueNotifier<String?>(null);
  static final ValueNotifier<String> activeLabel = ValueNotifier<String>('');
  static StreamSubscription<Position>? _sub;

  /// Resume tracking after the app was restarted.
  static Future<void> resume() async {
    final prefs = await SharedPreferences.getInstance();
    final ref = prefs.getString('tracking_trip');
    if (ref != null) await start(ref, prefs.getString('tracking_label') ?? '');
  }

  static Future<bool> start(String tripRef, String label) async {
    if (!await LocationHelper.ensurePermission()) return false;

    await _sub?.cancel();
    final settings = AndroidSettings(
      accuracy: LocationAccuracy.high,
      distanceFilter: Config.trackingDistanceMeters,
      intervalDuration: Config.trackingInterval,
      foregroundNotificationConfig: ForegroundNotificationConfig(
        notificationTitle: 'Oppah Driver - tracking trip',
        notificationText: label.isEmpty ? 'Your location is being recorded for this trip.' : 'Trip $label: your location is being recorded.',
        enableWakeLock: true,
        setOngoing: true,
      ),
    );

    _sub = Geolocator.getPositionStream(locationSettings: settings).listen((pos) {
      LocalDb.addPoint(tripRef, pos.latitude, pos.longitude, pos.accuracy, pos.speed < 0 ? 0 : pos.speed, pos.timestamp);
    }, onError: (_) {});

    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('tracking_trip', tripRef);
    await prefs.setString('tracking_label', label);
    activeTrip.value = tripRef;
    activeLabel.value = label;
    return true;
  }

  static Future<void> stop() async {
    await _sub?.cancel();
    _sub = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('tracking_trip');
    await prefs.remove('tracking_label');
    activeTrip.value = null;
    activeLabel.value = '';
  }

  /// True when [tripRef] (or the same trip under its other reference) is tracked.
  static bool isTracking(String tripRef, [String? otherRef]) =>
      activeTrip.value != null && (activeTrip.value == tripRef || activeTrip.value == otherRef);
}
