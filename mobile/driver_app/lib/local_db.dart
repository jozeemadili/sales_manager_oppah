import 'dart:convert';

import 'package:path/path.dart';
import 'package:sqflite/sqflite.dart';

/// On-phone storage for things not yet sent to the portal:
///  - queue:    trips / route plans / expenses / submits, sent in order
///  - points:   background tracking points
///  - trip_map: app trip uuid -> portal trip id (for trips created offline)
///  - failed:   entries the portal rejected, shown to the driver
class LocalDb {
  static Database? _db;

  static Future<Database> get db async {
    return _db ??= await openDatabase(
      join(await getDatabasesPath(), 'oppah_driver.db'),
      version: 1,
      onCreate: (db, _) async {
        await db.execute('CREATE TABLE queue (id INTEGER PRIMARY KEY AUTOINCREMENT, uuid TEXT, kind TEXT, trip_ref TEXT, payload TEXT, created_at TEXT)');
        await db.execute('CREATE TABLE points (id INTEGER PRIMARY KEY AUTOINCREMENT, trip_ref TEXT, lat REAL, lng REAL, accuracy REAL, speed REAL, recorded_at TEXT)');
        await db.execute('CREATE TABLE trip_map (uuid TEXT PRIMARY KEY, trip_id INTEGER)');
        await db.execute('CREATE TABLE failed (id INTEGER PRIMARY KEY AUTOINCREMENT, kind TEXT, message TEXT, payload TEXT, created_at TEXT)');
      },
    );
  }

  // --- queue -------------------------------------------------------------

  static Future<void> enqueue(String kind, String uuid, String? tripRef, Map<String, dynamic> payload) async {
    await (await db).insert('queue', {
      'uuid': uuid,
      'kind': kind,
      'trip_ref': tripRef,
      'payload': jsonEncode(payload),
      'created_at': DateTime.now().toIso8601String(),
    });
  }

  static Future<List<Map<String, dynamic>>> queue() async => (await db).query('queue', orderBy: 'id');

  static Future<void> dequeue(int id) async => (await db).delete('queue', where: 'id = ?', whereArgs: [id]);

  static Future<int> queueCount() async =>
      Sqflite.firstIntValue(await (await db).rawQuery('SELECT COUNT(*) FROM queue')) ?? 0;

  // --- failed ------------------------------------------------------------

  static Future<void> addFailed(String kind, String message, String payload) async {
    await (await db).insert('failed', {'kind': kind, 'message': message, 'payload': payload, 'created_at': DateTime.now().toIso8601String()});
  }

  static Future<List<Map<String, dynamic>>> failed() async => (await db).query('failed', orderBy: 'id DESC');

  static Future<void> clearFailed() async => (await db).delete('failed');

  // --- trips created offline ----------------------------------------------

  static Future<void> mapTrip(String uuid, int tripId) async =>
      (await db).insert('trip_map', {'uuid': uuid, 'trip_id': tripId}, conflictAlgorithm: ConflictAlgorithm.replace);

  /// Portal trip id for "id:12" or "uuid:abc" (null while not synced yet).
  static Future<int?> resolveTrip(String? ref) async {
    if (ref == null) return null;
    if (ref.startsWith('id:')) return int.tryParse(ref.substring(3));
    if (ref.startsWith('uuid:')) {
      final rows = await (await db).query('trip_map', where: 'uuid = ?', whereArgs: [ref.substring(5)]);
      return rows.isEmpty ? null : rows.first['trip_id'] as int;
    }
    return null;
  }

  /// The app uuid a portal trip was created under (if it was created by this phone).
  static Future<String?> uuidForTrip(int tripId) async {
    final rows = await (await db).query('trip_map', where: 'trip_id = ?', whereArgs: [tripId]);
    return rows.isEmpty ? null : rows.first['uuid'] as String;
  }

  /// Trips created on this phone that are not on the portal yet.
  static Future<List<Map<String, dynamic>>> unsyncedTrips() async {
    final rows = await (await db).query('queue', where: "kind = 'create_trip'", orderBy: 'id DESC');
    return rows.map((r) => jsonDecode(r['payload'] as String) as Map<String, dynamic>).toList();
  }

  /// Entries still waiting to be sent for a trip (either of its references).
  static Future<int> pendingFor(List<String> refs) async {
    if (refs.isEmpty) return 0;
    return Sqflite.firstIntValue(await (await db).rawQuery(
          'SELECT COUNT(*) FROM queue WHERE trip_ref IN (${List.filled(refs.length, '?').join(',')})', refs)) ??
        0;
  }

  // --- tracking points -----------------------------------------------------

  static Future<void> addPoint(String tripRef, double lat, double lng, double accuracy, double speed, DateTime at) async {
    await (await db).insert('points', {
      'trip_ref': tripRef,
      'lat': lat,
      'lng': lng,
      'accuracy': accuracy,
      'speed': speed,
      'recorded_at': at.toUtc().toIso8601String(),
    });
  }

  static Future<List<Map<String, dynamic>>> points({int limit = 200}) async =>
      (await db).query('points', orderBy: 'id', limit: limit);

  static Future<void> deletePoints(List<int> ids) async {
    if (ids.isEmpty) return;
    await (await db).delete('points', where: 'id IN (${List.filled(ids.length, '?').join(',')})', whereArgs: ids);
  }

  static Future<int> pointCount() async =>
      Sqflite.firstIntValue(await (await db).rawQuery('SELECT COUNT(*) FROM points')) ?? 0;

  static Future<void> clearAll() async {
    final d = await db;
    for (final t in ['queue', 'points', 'trip_map', 'failed']) {
      await d.delete(t);
    }
  }
}
