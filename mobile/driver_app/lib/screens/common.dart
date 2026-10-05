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
  final (bg, fg, label) = switch (status) {
    'Pending' => (const Color(0xFFFAEEDA), const Color(0xFF633806), 'Pending'),
    'submitted' => (const Color(0xFFEAF3DE), const Color(0xFF27500A), 'Submitted'),
    'Waiting to sync' => (const Color(0xFFF1EFE8), const Color(0xFF444441), 'Waiting to sync'),
    _ => (const Color(0xFFF1EFE8), const Color(0xFF444441), status),
  };
  return Container(
    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
    decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(12)),
    child: Text(label, style: TextStyle(color: fg, fontSize: 12, fontWeight: FontWeight.w500)),
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
