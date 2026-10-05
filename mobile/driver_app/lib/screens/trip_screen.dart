import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:uuid/uuid.dart';

import '../api.dart';
import '../local_db.dart';
import '../location_helper.dart';
import '../session.dart';
import '../sync_service.dart';
import '../tracker.dart';
import '../theme.dart';
import 'common.dart';
import 'expense_screen.dart';
import 'route_plan_screen.dart';

class TripScreen extends StatefulWidget {
  final String tripRef; // "id:12" (on the portal) or "uuid:..." (created offline)
  final Map<String, dynamic> summary;

  const TripScreen({super.key, required this.tripRef, required this.summary});

  @override
  State<TripScreen> createState() => _TripScreenState();
}

class _TripScreenState extends State<TripScreen> {
  Map<String, dynamic> _trip = {};
  String? _otherRef; // same trip under its other reference
  int _pending = 0;
  bool _busy = false;

  bool get _onPortal => _trip['id'] != null;
  bool get _open => (_trip['status'] ?? 'Pending') == 'Pending';
  String get _label => (_trip['trip_no'] ?? 'new trip').toString();

  @override
  void initState() {
    super.initState();
    _trip = Map<String, dynamic>.from(widget.summary);
    SyncService.changes.addListener(_load);
    _load();
  }

  @override
  void dispose() {
    SyncService.changes.removeListener(_load);
    super.dispose();
  }

  Future<void> _load() async {
    final tripId = await LocalDb.resolveTrip(widget.tripRef);
    if (widget.tripRef.startsWith('id:') && tripId != null) {
      final uuid = await LocalDb.uuidForTrip(tripId);
      _otherRef = uuid == null ? null : 'uuid:$uuid';
    } else if (tripId != null) {
      _otherRef = 'id:$tripId';
    }

    if (tripId != null) {
      final prefs = await SharedPreferences.getInstance();
      try {
        final res = await Session.api.get('trips/$tripId');
        _trip = Map<String, dynamic>.from(res['trip']);
        await prefs.setString('trip_$tripId', jsonEncode(_trip));
      } on ApiException catch (e) {
        if (e.unauthorized) return SyncService.onUnauthorized?.call();
        final cached = prefs.getString('trip_$tripId');
        if (cached != null) _trip = Map<String, dynamic>.from(jsonDecode(cached));
      }
    }
    _pending = await LocalDb.pendingFor([widget.tripRef, ?_otherRef]);
    if (mounted) setState(() {});
  }

  Future<void> _add(Widget form) async {
    final saved = await Navigator.of(context).push<bool>(MaterialPageRoute(builder: (_) => form));
    if (saved == true) {
      SyncService.run();
      _load();
    }
  }

  Future<void> _toggleTracking() async {
    if (Tracker.isTracking(widget.tripRef, _otherRef)) {
      await Tracker.stop();
    } else {
      final ok = await Tracker.start(widget.tripRef, _label);
      if (!ok && mounted) {
        toast(context, 'Allow location and turn on GPS to track. / Ruhusu mahali na washa GPS.', error: true);
      }
    }
    setState(() {});
  }

  Future<void> _submit() async {
    final ok = await showDialog<bool>(
      context: context,
      builder: (c) => AlertDialog(
        title: const Text('Send To Stock?'),
        content: const Text('After sending you cannot add route plans or expenses to this trip, and tracking stops.\nBaada ya kutuma huwezi kuongeza tena.'),
        actions: [
          TextButton(onPressed: () => Navigator.pop(c, false), child: const Text('Cancel')),
          FilledButton(onPressed: () => Navigator.pop(c, true), child: const Text('Send / Tuma')),
        ],
      ),
    );
    if (ok != true) return;
    setState(() => _busy = true);
    final geo = await LocationHelper.geoFields();
    await LocalDb.enqueue('submit', const Uuid().v4(), widget.tripRef, {...geo});
    if (Tracker.isTracking(widget.tripRef, _otherRef)) await Tracker.stop();
    _trip['status'] = 'submitted';
    SyncService.run(); // sends in the background; saving never waits for the network
    await _load();
    if (mounted) {
      setState(() => _busy = false);
      toast(context, _pending > 0 ? 'Saved on phone - will send when online.' : 'Sent to stock. / Imetumwa.');
    }
  }

  @override
  Widget build(BuildContext context) {
    final plans = List<Map<String, dynamic>>.from(_trip['route_plans'] ?? const []);
    final expenses = List<Map<String, dynamic>>.from(_trip['expenses'] ?? const []);
    final totals = Map<String, dynamic>.from(_trip['totals'] ?? const {});
    final tracking = Tracker.isTracking(widget.tripRef, _otherRef);

    return Scaffold(
      appBar: AppBar(title: LogoTitle(_label)),
      body: RefreshIndicator(
        onRefresh: () async {
          await SyncService.run();
          await _load();
        },
        child: ListView(
          padding: const EdgeInsets.all(12),
          children: [
            Card(
              child: Padding(
                padding: const EdgeInsets.all(14),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(children: [
                      Expanded(child: Text('${_trip['going_customer'] ?? ''}${(_trip['return_customer'] ?? '').toString().isNotEmpty ? '  →  ${_trip['return_customer']}' : ''}', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold))),
                      statusChip(_onPortal ? (_trip['status'] ?? '').toString() : 'Waiting to sync'),
                    ]),
                    Text('Date: ${_trip['route_date'] ?? ''}   ${_trip['truck'] != null ? 'Truck: ${_trip['truck']}' : ''}'),
                    if (totals.isNotEmpty) ...[
                      const Divider(),
                      Text('Transport fee: ${money(totals['transport_fee'])} TZS'),
                      Text('Route plans / fuel: ${money(totals['route_plans'])} TZS'),
                      Text('Expenses: ${money(totals['expenses'])} TZS'),
                    ],
                    if (_pending > 0) ...[
                      const SizedBox(height: 6),
                      Text('$_pending entries waiting to send', style: const TextStyle(color: Colors.orange)),
                    ],
                  ],
                ),
              ),
            ),
            if (_open)
              Card(
                color: tracking ? const Color(0xFFE6F1FB) : null,
                child: SwitchListTile(
                  title: Text(tracking ? 'Tracking ON / Inafuatiliwa' : 'Tracking OFF'),
                  subtitle: const Text('Records your location during this trip'),
                  value: tracking,
                  onChanged: (_) => _toggleTracking(),
                ),
              ),
            const SizedBox(height: 8),
            _section('Route plans / fuel', plans.map((p) => ListTile(
                  dense: true,
                  title: Text('${p['from_location']} → ${p['to_location']}'),
                  subtitle: Text('${p['distance_km']} km · ${p['fuel_litres']} L'),
                  trailing: Text(money(p['amount_tsh'])),
                ))),
            if (_open)
              OutlinedButton.icon(icon: const Icon(Icons.local_gas_station), label: const Text('Add route plan / fuel'), onPressed: () => _add(RoutePlanScreen(tripRef: widget.tripRef))),
            const SizedBox(height: 12),
            _section('Expenses / Matumizi', expenses.map((e) => ListTile(
                  dense: true,
                  title: Text('${e['name'] ?? 'Expense'}'),
                  subtitle: (e['desr'] ?? '').toString().isEmpty ? null : Text('${e['desr']}'),
                  trailing: Text(money(e['amount_used'])),
                ))),
            if (_open)
              OutlinedButton.icon(icon: const Icon(Icons.receipt_long), label: const Text('Add expense'), onPressed: () => _add(ExpenseScreen(tripRef: widget.tripRef))),
            const SizedBox(height: 24),
            if (_open) saveButton('Send To Stock / Tuma', _busy, _submit),
          ],
        ),
      ),
    );
  }

  Widget _section(String title, Iterable<Widget> rows) => Card(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Padding(padding: const EdgeInsets.fromLTRB(14, 12, 14, 4), child: Text(title, style: const TextStyle(fontWeight: FontWeight.bold))),
            if (rows.isEmpty)
              Padding(
                padding: const EdgeInsets.fromLTRB(14, 0, 14, 12),
                child: Text(_onPortal ? 'None yet' : 'Shown after the trip is sent', style: const TextStyle(color: Colors.black54)),
              )
            else
              ...rows,
          ],
        ),
      );
}
