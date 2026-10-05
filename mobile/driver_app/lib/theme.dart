import 'package:flutter/material.dart';

/// Design C "Clean white": white screens, the Oppah logo in the header,
/// green (from the logo) for buttons and blue for tracking status.
class OppahTheme {
  static const green = Color(0xFF2E9E44);
  static const blue = Color(0xFF185FA5);
  static const ink = Color(0xFF1F2A24);
  static const muted = Color(0xFF5F5E5A);
  static const line = Color(0xFFD3D1C7);
  static const soft = Color(0xFFF1EFE8);

  static ThemeData get light {
    final scheme = ColorScheme.fromSeed(seedColor: green, primary: green, surface: Colors.white, brightness: Brightness.light);
    return ThemeData(
      useMaterial3: true,
      fontFamily: 'Roboto',
      colorScheme: scheme,
      scaffoldBackgroundColor: Colors.white,
      appBarTheme: const AppBarTheme(
        backgroundColor: Colors.white,
        foregroundColor: ink,
        elevation: 0,
        scrolledUnderElevation: 0,
        surfaceTintColor: Colors.transparent,
        titleTextStyle: TextStyle(fontFamily: 'Roboto', color: ink, fontSize: 18, fontWeight: FontWeight.w500),
        shape: Border(bottom: BorderSide(color: line, width: 0.5)),
      ),
      cardTheme: CardThemeData(
        color: Colors.white,
        elevation: 0,
        surfaceTintColor: Colors.transparent,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12), side: const BorderSide(color: line, width: 0.5)),
      ),
      filledButtonTheme: FilledButtonThemeData(
        style: FilledButton.styleFrom(backgroundColor: green, foregroundColor: Colors.white, shape: const StadiumBorder()),
      ),
      outlinedButtonTheme: OutlinedButtonThemeData(
        style: OutlinedButton.styleFrom(foregroundColor: green, side: const BorderSide(color: line), shape: const StadiumBorder(), minimumSize: const Size.fromHeight(46)),
      ),
      floatingActionButtonTheme: const FloatingActionButtonThemeData(backgroundColor: green, foregroundColor: Colors.white),
      inputDecorationTheme: const InputDecorationTheme(
        focusedBorder: OutlineInputBorder(borderSide: BorderSide(color: green, width: 1.5)),
      ),
      switchTheme: SwitchThemeData(
        thumbColor: WidgetStateProperty.resolveWith((s) => s.contains(WidgetState.selected) ? Colors.white : null),
        trackColor: WidgetStateProperty.resolveWith((s) => s.contains(WidgetState.selected) ? green : null),
      ),
    );
  }
}

/// Header title: small Oppah logo next to the page name.
class LogoTitle extends StatelessWidget {
  final String text;

  const LogoTitle(this.text, {super.key});

  @override
  Widget build(BuildContext context) => Row(mainAxisSize: MainAxisSize.min, children: [
        Image.asset('assets/logo.png', height: 34),
        const SizedBox(width: 10),
        Flexible(child: Text(text, overflow: TextOverflow.ellipsis)),
      ]);
}
