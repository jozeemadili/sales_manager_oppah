import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

final _money = NumberFormat('#,##0', 'en');

String money(dynamic v) => _money.format((v is num) ? v : num.tryParse('$v') ?? 0);

void toast(BuildContext context, String message, {bool error = false}) {
  ScaffoldMessenger.of(context).showSnackBar(SnackBar(
    content: Text(message),
    backgroundColor: error ? Colors.red.shade700 : null,
  ));
}

Widget statusChip(String status) {
  final color = switch (status) {
    'Pending' => Colors.orange,
    'submitted' => Colors.green,
    'Waiting to sync' => Colors.blueGrey,
    _ => Colors.grey,
  };
  return Chip(
    label: Text(status == 'submitted' ? 'Submitted' : status, style: const TextStyle(color: Colors.white, fontSize: 12)),
    backgroundColor: color,
    visualDensity: VisualDensity.compact,
    side: BorderSide.none,
  );
}

/// Numeric text field with a label.
Widget numberField(TextEditingController c, String label, {bool required = true}) => TextFormField(
      controller: c,
      keyboardType: const TextInputType.numberWithOptions(decimal: true),
      decoration: InputDecoration(labelText: label, border: const OutlineInputBorder()),
      validator: (v) {
        if (!required && (v == null || v.trim().isEmpty)) return null;
        return double.tryParse((v ?? '').replaceAll(',', '')) == null ? 'Enter a number / Weka namba' : null;
      },
    );

Widget textField(TextEditingController c, String label, {bool required = true, int lines = 1}) => TextFormField(
      controller: c,
      maxLines: lines,
      decoration: InputDecoration(labelText: label, border: const OutlineInputBorder()),
      validator: (v) => required && (v == null || v.trim().isEmpty) ? 'Required / Inahitajika' : null,
    );

double? parseNum(TextEditingController c) => double.tryParse(c.text.replaceAll(',', '').trim());

/// Full-width primary button that shows a spinner while [busy].
Widget saveButton(String label, bool busy, VoidCallback onPressed) => SizedBox(
      width: double.infinity,
      height: 50,
      child: FilledButton(
        onPressed: busy ? null : onPressed,
        child: busy ? const SizedBox(width: 22, height: 22, child: CircularProgressIndicator(strokeWidth: 2.5)) : Text(label),
      ),
    );
