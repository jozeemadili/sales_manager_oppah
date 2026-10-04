import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../api.dart';
import '../local_db.dart';
import '../session.dart';
import '../sync_service.dart';
import '../tracker.dart';
import 'common.dart';
import 'login_screen.dart';
import 'new_trip_screen.dart';
import 'trip_screen.dart';

class TripsScreen extends StatefulWidget {
  const TripsScreen({super.key});

  @override
  State<TripsScreen> createState() => _TripsScreenState();
}

class _TripsScreenState extends State<TripsScreen> {
  List<Map<String, dynamic>> _trips = [];
  List<Map<String, dynamic>> _local = [];
  List<Map<String, dynamic>> _failed = [];
  bool _offline = false;
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    SyncService.changes.addListener(_load);
    _load();
  }

  @override
  void dispose() {
    SyncService.changes.removeListener(_load);
    super.dispose();
  }

  Future<void> _load() async {
    final prefs = await SharedPreferences.getInstance();
    try {
      await Session.refreshProfile();
      final res = await Session.api.get('trips');
      _trips = List<Map<String, dynamic>>.from(res['trips']);
      await prefs.setString('trips_cache', jsonEncode(_trips));
      _offline = false;
    } on ApiException catch (e) {
      if (e.unauthorized) return SyncService.onUnauthorized?.call();
      final cached = prefs.getString('trips_cache');
      if (cached != null) _trips = List<Map<String, dynamic>>.from(jsonDecode(cached));
      _offline = true;
    }
    _local = await LocalDb.unsyncedTrips();
    _failed = await LocalDb.failed();
    await SyncService.refreshCounts();
    if (mounted) setState(() => _loading = false);
  }

  Future<void> _refresh() async {
    await SyncService.run();
    await _load();
  }

  Future<void> _logout() async {
    final ok = await showDialog<bool>(
      context: context,
      builder: (c) => AlertDialog(
        title: const Text('Log out? / Toka?'),
        content: Text(SyncService.pending.value + SyncService.pendingPoints.value > 0
            ? 'Some entries are not sent yet and will be lost. Connect to internet first.\nBaadhi ya taarifa hazijatumwa.'
            : 'Tracking will stop.'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(c, false), child: const Text('Cancel')),
          FilledButton(onPressed: () => Navigator.pop(c, true), child: const Text('Log out')),
        ],
      ),
    );
    if (ok != true) return;
    await Tracker.stop();
    SyncService.stop();
    await Session.logout();
    if (!mounted) return;
    Navigator.of(context).pushAndRemoveUntil(MaterialPageRoute(builder: (_) => const LoginScreen()), (_) => false);
  }

  void _open(String ref, Map<String, dynamic> summary) async {
    await Navigator.of(context).push(MaterialPageRoute(builder: (_) => TripScreen(tripRef: ref, summary: summary)));
    _load();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('My Trips / Safari zangu'),
        actions: [IconButton(icon: const Icon(Icons.logout), onPressed: _logout, tooltip: 'Log out')],
      ),
      floatingActionButton: FloatingActionButton.extended(
        icon: const Icon(Icons.add),
        label: const Text('New trip'),
        onPressed: Session.trucks.isEmpty
            ? () => toast(context, 'No truck is assigned to you. Ask the office. / Huna gari.', error: true)
            : () async {
                final ref = await Navigator.of(context).push<String>(MaterialPageRoute(builder: (_) => const NewTripScreen()));
                await _load();
                if (ref != null && mounted) {
                  final local = _local.firstWhere((t) => 'uuid:${t['uuid']}' == ref, orElse: () => {});
                  _open(ref, local);
                }
              },
      ),
      body: RefreshIndicator(
        onRefresh: _refresh,
        child: _loading
            ? const Center(child: CircularProgressIndicator())
            : ListView(
                padding: const EdgeInsets.only(bottom: 90),
                children: [
                  _statusBar(),
                  if (_failed.isNotEmpty) _failedCard(),
                  for (final t in _local) _tripTile(t, 'uuid:${t['uuid']}', local: true),
                  for (final t in _trips) _tripTile(t, 'id:${t['id']}'),
                  if (_trips.isEmpty && _local.isEmpty)
                    const Padding(padding: EdgeInsets.all(40), child: Text('No trips yet. Tap "New trip".\nHakuna safari bado.', textAlign: TextAlign.center)),
                ],
              ),
      ),
    );
  }

  Widget _statusBar() {
    return Card(
      margin: const EdgeInsets.fromLTRB(12, 12, 12, 4),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(Session.driverName, style: const TextStyle(fontWeight: FontWeight.bold)),
            Text('Truck: ${Session.trucks.map((t) => t['plate_no']).join(', ')}'),
            const SizedBox(height: 6),
            ValueListenableBuilder<String?>(
              valueListenable: Tracker.activeTrip,
              builder: (_, active, _) => Row(children: [
                Icon(active != null ? Icons.my_location : Icons.location_disabled, size: 18, color: active != null ? Colors.green : Colors.grey),
                const SizedBox(width: 6),
                Expanded(child: Text(active != null ? 'Tracking trip ${Tracker.activeLabel.value}' : 'Not tracking')),
              ]),
            ),
            AnimatedBuilder(
              animation: Listenable.merge([SyncService.pending, SyncService.pendingPoints]),
              builder: (_, _) {
                final waiting = SyncService.pending.value;
                final points = SyncService.pendingPoints.value;
                return Row(children: [
                  Icon(_offline ? Icons.cloud_off : Icons.cloud_done, size: 18, color: _offline ? Colors.orange : Colors.green),
                  const SizedBox(width: 6),
                  Expanded(
                    child: Text(waiting + points == 0
                        ? (_offline ? 'Offline - everything saved on phone' : 'All sent / Zote zimetumwa')
                        : 'Waiting to send: $waiting entries, $points location points'),
                  ),
                ]);
              },
            ),
          ],
        ),
      ),
    );
  }

  Widget _failedCard() {
    return Card(
      color: Colors.red.shade50,
      margin: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Not accepted by the office system:', style: TextStyle(fontWeight: FontWeight.bold)),
            for (final f in _failed.take(5)) Text('• ${f['kind']}: ${f['message']}'),
            Align(
              alignment: Alignment.centerRight,
              child: TextButton(onPressed: () async {
                await LocalDb.clearFailed();
                _load();
              }, child: const Text('Dismiss')),
            ),
          ],
        ),
      ),
    );
  }

  Widget _tripTile(Map<String, dynamic> t, String ref, {bool local = false}) {
    final status = local ? 'Waiting to sync' : (t['status'] ?? '').toString();
    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
      child: ListTile(
        leading: ValueListenableBuilder<String?>(
          valueListenable: Tracker.activeTrip,
          builder: (_, active, _) => Icon(active == ref ? Icons.my_location : Icons.local_shipping, color: active == ref ? Colors.green : null),
        ),
        title: Text(local ? 'New trip (not sent yet)' : '${t['trip_no']}'),
        subtitle: Text('${t['route_date']} · ${t['going_customer'] ?? ''}${(t['return_customer'] ?? '').toString().isNotEmpty ? ' ⇄ ${t['return_customer']}' : ''}'),
        trailing: statusChip(status),
        onTap: () => _open(ref, t),
      ),
    );
  }
}
