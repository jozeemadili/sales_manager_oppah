import 'package:flutter/material.dart';
import 'package:uuid/uuid.dart';

import '../local_db.dart';
import '../location_helper.dart';
import '../theme.dart';
import 'common.dart';

class RoutePlanScreen extends StatefulWidget {
  final String tripRef;

  const RoutePlanScreen({super.key, required this.tripRef});

  @override
  State<RoutePlanScreen> createState() => _RoutePlanScreenState();
}

class _RoutePlanScreenState extends State<RoutePlanScreen> {
  final _form = GlobalKey<FormState>();
  final _from = TextEditingController();
  final _to = TextEditingController();
  final _km = TextEditingController();
  final _litres = TextEditingController();
  final _amount = TextEditingController();
  final _desc = TextEditingController();
  bool _busy = false;

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() => _busy = true);
    final uuid = const Uuid().v4();
    await LocalDb.enqueue('route_plan', uuid, widget.tripRef, {
      'uuid': uuid,
      'from_location': _from.text.trim(),
      'to_location': _to.text.trim(),
      'distance_km': parseNum(_km),
      'fuel_litres': parseNum(_litres),
      'amount_tsh': parseNum(_amount),
      'description': _desc.text.trim(),
      ...await LocationHelper.geoFields(),
    });
    if (mounted) Navigator.of(context).pop(true);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const LogoTitle('Route plan / fuel')),
      body: Form(
        key: _form,
        child: ListView(padding: const EdgeInsets.all(16), children: [
          textField(_from, 'From / Kutoka'),
          const SizedBox(height: 14),
          textField(_to, 'To / Kwenda'),
          const SizedBox(height: 14),
          numberField(_km, 'Distance (km)'),
          const SizedBox(height: 14),
          numberField(_litres, 'Fuel (litres)'),
          const SizedBox(height: 14),
          numberField(_amount, 'Amount (TZS)'),
          const SizedBox(height: 14),
          textField(_desc, 'Description (optional)', required: false, lines: 2),
          const SizedBox(height: 22),
          saveButton('Save / Hifadhi', _busy, _save),
        ]),
      ),
    );
  }
}
