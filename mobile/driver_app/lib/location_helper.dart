import 'package:geolocator/geolocator.dart';

/// Location permission + a single position for an action (trip created,
/// route plan, expense, submit). Never throws: without a fix it returns the
/// reason, and the action is still saved ("no location").
class LocationHelper {
  /// Ask for permission once (Android dialog). Returns false if refused.
  static Future<bool> ensurePermission() async {
    if (!await Geolocator.isLocationServiceEnabled()) return false;
    var permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
    }
    return permission == LocationPermission.always || permission == LocationPermission.whileInUse;
  }

  /// geo_* fields the portal API expects with each action.
  static Future<Map<String, dynamic>> geoFields() async {
    try {
      if (!await Geolocator.isLocationServiceEnabled()) return {'geo_status': 'unavailable'};
      var permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.denied || permission == LocationPermission.deniedForever) {
        return {'geo_status': 'denied'};
      }
      // A fix from the last 2 minutes (e.g. from tracking) is good enough and instant.
      final recent = await Geolocator.getLastKnownPosition();
      if (recent != null && DateTime.now().difference(recent.timestamp) < const Duration(minutes: 2)) {
        return {'geo_status': 'ok', 'geo_lat': recent.latitude, 'geo_lng': recent.longitude, 'geo_accuracy': recent.accuracy.round()};
      }
      final pos = await Geolocator.getCurrentPosition(
        locationSettings: const LocationSettings(accuracy: LocationAccuracy.high, timeLimit: Duration(seconds: 10)),
      );
      return {'geo_status': 'ok', 'geo_lat': pos.latitude, 'geo_lng': pos.longitude, 'geo_accuracy': pos.accuracy.round()};
    } catch (e) {
      final last = await Geolocator.getLastKnownPosition();
      if (last != null) {
        return {'geo_status': 'ok', 'geo_lat': last.latitude, 'geo_lng': last.longitude, 'geo_accuracy': last.accuracy.round()};
      }
      return {'geo_status': e.toString().contains('TimeLimit') ? 'timeout' : 'unavailable'};
    }
  }
}
