import 'package:flutter/material.dart';
import 'package:uuid/uuid.dart';

import '../local_db.dart';
import '../location_helper.dart';
import '../session.dart';
import 'common.dart';

class ExpenseScreen extends StatefulWidget {
  final String tripRef;

  const ExpenseScreen({super.key, required this.tripRef});

  @override
  State<ExpenseScreen> createState() => _ExpenseScreenState();
}

class _ExpenseScreenState extends State<ExpenseScreen> {
  final _form = GlobalKey<FormState>();
  final _amount = TextEditingController();
  final _desc = TextEditingController();
  int? _typeId;
  bool _busy = false;

  Future<void> _save() async {
    if (!_form.currentState!.validate()) return;
    setState(() => _busy = true);
    final uuid = const Uuid().v4();
    await LocalDb.enqueue('expense', uuid, widget.tripRef, {
      'uuid': uuid,
      'expense_id': _typeId,
      'amount_used': parseNum(_amount),
      'desr': _desc.text.trim(),
      ...await LocationHelper.geoFields(),
    });
    if (mounted) Navigator.of(context).pop(true);
  }

  @override
  Widget build(BuildContext context) {
    final types = Session.expenseTypes;
    return Scaffold(
      appBar: AppBar(title: const Text('Trip expense / Matumizi')),
      body: Form(
        key: _form,
        child: ListView(padding: const EdgeInsets.all(16), children: [
          DropdownButtonFormField<int>(
            initialValue: _typeId,
            decoration: const InputDecoration(labelText: 'Expense type / Aina', border: OutlineInputBorder()),
            items: [for (final t in types) DropdownMenuItem(value: (t['id'] as num).toInt(), child: Text('${t['name']}'.toUpperCase()))],
            onChanged: (v) => setState(() => _typeId = v),
            validator: (v) => v == null ? 'Choose a type' : null,
          ),
          if (types.isEmpty)
            const Padding(padding: EdgeInsets.only(top: 6), child: Text('No expense types yet - ask the office to add GARI expense types.', style: TextStyle(color: Colors.red))),
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
