import 'package:flutter/material.dart';

import 'screens/login_screen.dart';
import 'screens/trips_screen.dart';
import 'session.dart';
import 'theme.dart';
import 'sync_service.dart';
import 'tracker.dart';

final navigatorKey = GlobalKey<NavigatorState>();

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  final loggedIn = await Session.restore();
  if (loggedIn) {
    SyncService.start();
    Tracker.resume();
  }
  SyncService.onUnauthorized = () async {
    await Tracker.stop();
    SyncService.stop();
    await Session.logout();
    navigatorKey.currentState?.pushAndRemoveUntil(MaterialPageRoute(builder: (_) => const LoginScreen()), (_) => false);
  };
  runApp(OppahDriverApp(loggedIn: loggedIn));
}

class OppahDriverApp extends StatelessWidget {
  final bool loggedIn;

  const OppahDriverApp({super.key, required this.loggedIn});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Oppah',
      navigatorKey: navigatorKey,
      debugShowCheckedModeBanner: false,
      theme: OppahTheme.light,
      home: loggedIn ? const TripsScreen() : const LoginScreen(),
    );
  }
}
