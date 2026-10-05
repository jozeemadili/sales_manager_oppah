import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:uuid/uuid.dart';

import '../local_db.dart';
import '../location_helper.dart';
import '../session.dart';
import '../sync_service.dart';
import '../tracker.dart';
import '../theme.dart';
import 'common.dart';

/// New trip (like Trips > New on the portal). Saved on the phone first, sent
/// when online; tracking starts straight away. Returns the trip reference.
class NewTripScreen extends StatefulWidget {
  const NewTripScreen({super.key});

  @override
  State<NewTripScreen> createState() => _NewTripScreenState();
}

class _NewTripScreenState extends State<NewTripScreen> {
  final _form = GlobalKey<FormState>();
  final _going = TextEditingController();
  final _return = TextEditingController();
  final _goingFee = TextEditingController();
  final _returnFee = TextEditingController();
  DateTime _date = DateTime.now();
  int? _truckId;
  bool _busy = false;

  @override
  void initState() {
    super.initState();
    if (Session.trucks.isNotEmpty) _truckId = (Session.trucks.first['id'] as num).toInt();
  }

  Future<void> _save() async {
    if (!_form.currentState!.validate() || _truckId == null) return;
    setState(() => _busy = true);
    final uuid = const Uuid().v4();
    final geo = await LocationHelper.geoFields();
    final payload = {
      'uuid': uuid,
      'route_date': DateFormat('yyyy-MM-dd').format(_date),
      'truck_id': _truckId,
      'going_customer': _going.text.trim(),
      'return_customer': _return.text.trim(),
      'going_transport_fee': parseNum(_goingFee),
      'return_transport_fee': parseNum(_returnFee),
      ...geo,
    };
    await LocalDb.enqueue('create_trip', uuid, null, payload);
    final ref = 'uuid:$uuid';
    final tracking = await Tracker.start(ref, 'new trip');
    SyncService.run(); // sends in the background; saving never waits for the network
    if (!mounted) return;
    toast(context, tracking ? 'Trip saved. Tracking started. / Safari imehifadhiwa.' : 'Trip saved. Turn on GPS to track.', error: !tracking);
    Navigator.of(context).pop(ref);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const LogoTitle('New trip / Safari mpya')),
      body: Form(
        key: _form,
        child: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            ListTile(
              contentPadding: EdgeInsets.zero,
              leading: const Icon(Icons.event),
              title: const Text('Route date'),
              subtitle: Text(DateFormat('EEE, d MMM yyyy').format(_date)),
              trailing: const Icon(Icons.edit_calendar),
              onTap: () async {
                final picked = await showDatePicker(context: context, initialDate: _date, firstDate: DateTime.now().subtract(const Duration(days: 30)), lastDate: DateTime.now().add(const Duration(days: 30)));
                if (picked != null) setState(() => _date = picked);
              },
            ),
            const SizedBox(height: 8),
            DropdownButtonFormField<int>(
              initialValue: _truckId,
              decoration: const InputDecoration(labelText: 'Truck / Gari', border: OutlineInputBorder()),
              items: [for (final t in Session.trucks) DropdownMenuItem(value: (t['id'] as num).toInt(), child: Text('${t['plate_no']}'))],
              onChanged: (v) => setState(() => _truckId = v),
              validator: (v) => v == null ? 'Choose truck' : null,
            ),
            const SizedBox(height: 14),
            textField(_going, 'Going customer / Mteja wa kwenda'),
            const SizedBox(height: 14),
            numberField(_goingFee, 'Going transport fee (TZS)', required: false),
            const SizedBox(height: 14),
            textField(_return, 'Return customer / Mteja wa kurudi', required: false),
            const SizedBox(height: 14),
            numberField(_returnFee, 'Return transport fee (TZS)', required: false),
            const SizedBox(height: 10),
            const Text('📍 Your location is recorded when you save and during the trip.', style: TextStyle(fontSize: 12, color: Colors.black54)),
            const SizedBox(height: 18),
            saveButton('Save trip / Hifadhi', _busy, _save),
          ],
        ),
      ),
    );
  }
}
