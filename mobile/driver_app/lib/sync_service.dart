import 'dart:async';
import 'dart:convert';

import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:flutter/foundation.dart';

import 'api.dart';
import 'config.dart';
import 'local_db.dart';
import 'session.dart';

/// Sends queued entries (in order) and tracking points whenever the phone is
/// online: on start, every minute, when the network comes back, and right
/// after the driver saves something. Each entry carries a uuid, so a retry
/// after a dropped connection is never saved twice by the portal.
class SyncService {
  static final ValueNotifier<int> pending = ValueNotifier<int>(0);
  static final ValueNotifier<int> pendingPoints = ValueNotifier<int>(0);
  static final ValueNotifier<int> changes = ValueNotifier<int>(0); // bumped after successful sends
  static void Function()? onUnauthorized;

  static Timer? _timer;
  static StreamSubscription? _conn;
  static bool _running = false;

  static void start() {
    _timer ??= Timer.periodic(Config.syncInterval, (_) => run());
    _conn ??= Connectivity().onConnectivityChanged.listen((_) => run());
    run();
  }

  static void stop() {
    _timer?.cancel();
    _timer = null;
    _conn?.cancel();
    _conn = null;
  }

  static Future<void> refreshCounts() async {
    pending.value = await LocalDb.queueCount();
    pendingPoints.value = await LocalDb.pointCount();
  }

  static Future<void> run() async {
    if (_running || Session.api.token == null) return;
    _running = true;
    var sent = false;
    try {
      sent = await _sendQueue() | await _sendPoints();
    } on ApiException catch (e) {
      if (e.unauthorized) onUnauthorized?.call();
    } finally {
      _running = false;
      await refreshCounts();
      if (sent) changes.value++;
    }
  }

  static Future<bool> _sendQueue() async {
    var sent = false;
    for (final item in await LocalDb.queue()) {
      final kind = item['kind'] as String;
      final payload = jsonDecode(item['payload'] as String) as Map<String, dynamic>;

      int? tripId;
      if (kind != 'create_trip') {
        tripId = await LocalDb.resolveTrip(item['trip_ref'] as String?);
        if (tripId == null) break; // its trip is not on the portal yet: keep order, try later
      }

      try {
        switch (kind) {
          case 'create_trip':
            final res = await Session.api.post('trips', payload);
            await LocalDb.mapTrip(payload['uuid'], (res['trip']['id'] as num).toInt());
            break;
          case 'route_plan':
            await Session.api.post('trips/$tripId/route-plans', payload);
            break;
          case 'expense':
            await Session.api.post('trips/$tripId/expenses', payload);
            break;
          case 'submit':
            await Session.api.post('trips/$tripId/submit', payload);
            break;
        }
        await LocalDb.dequeue(item['id'] as int);
        sent = true;
      } on ApiException catch (e) {
        if (e.network || e.unauthorized) rethrow; // offline / logged out: stop and retry later
        // Rejected by the portal (duplicate, already submitted...): show it, don't block the queue.
        await LocalDb.addFailed(kind, e.message, item['payload'] as String);
        await LocalDb.dequeue(item['id'] as int);
      }
    }
    return sent;
  }

  static Future<bool> _sendPoints() async {
    var sent = false;
    while (true) {
      final rows = await LocalDb.points(limit: 200);
      if (rows.isEmpty) break;

      final batch = <Map<String, dynamic>>[];
      final ids = <int>[];
      for (final r in rows) {
        final tripId = await LocalDb.resolveTrip(r['trip_ref'] as String?);
        if (tripId == null) continue; // trip still offline-only
        batch.add({
          'trip_id': tripId,
          'lat': r['lat'],
          'lng': r['lng'],
          'accuracy': r['accuracy'],
          'speed': r['speed'],
          'recorded_at': r['recorded_at'],
        });
        ids.add(r['id'] as int);
      }
      if (batch.isEmpty) break;

      await Session.api.post('locations', {'points': batch});
      await LocalDb.deletePoints(ids);
      sent = true;
      if (rows.length < 200) break;
    }
    return sent;
  }
}
