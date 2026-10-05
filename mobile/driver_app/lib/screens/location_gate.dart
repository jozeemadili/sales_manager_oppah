import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';

import '../location_helper.dart';
import '../theme.dart';
import 'trips_screen.dart';

/// The app can only be used with location allowed and GPS on. Re-checked
/// every time the app comes back to the foreground (e.g. from Settings).
class LocationGate extends StatefulWidget {
  const LocationGate({super.key});

  @override
  State<LocationGate> createState() => _LocationGateState();
}

class _LocationGateState extends State<LocationGate> with WidgetsBindingObserver {
  String? _problem;
  bool _checking = true;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    _check(ask: true);
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) _check();
  }

  Future<void> _check({bool ask = false}) async {
    if (ask && await Geolocator.checkPermission() == LocationPermission.denied) {
      await Geolocator.requestPermission();
    }
    final problem = await LocationHelper.problem();
    if (mounted) {
      setState(() {
        _problem = problem;
        _checking = false;
      });
    }
  }

  Future<void> _fix() async {
    if (!await Geolocator.isLocationServiceEnabled()) {
      await Geolocator.openLocationSettings();
      return;
    }
    final permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.deniedForever) {
      await Geolocator.openAppSettings();
    } else {
      await Geolocator.requestPermission();
      await _check();
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_checking) return const Scaffold(body: Center(child: CircularProgressIndicator()));
    if (_problem == null) return const TripsScreen();

    return Scaffold(
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(28),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Image.asset('assets/logo.png', height: 110),
              const SizedBox(height: 24),
              const Icon(Icons.location_off, size: 56, color: Colors.redAccent),
              const SizedBox(height: 12),
              const Text('Location is required', textAlign: TextAlign.center, style: TextStyle(fontSize: 20, fontWeight: FontWeight.w500)),
              const Text('Mahali panahitajika', textAlign: TextAlign.center, style: TextStyle(color: OppahTheme.muted)),
              const SizedBox(height: 14),
              Text(_problem!, textAlign: TextAlign.center),
              const SizedBox(height: 8),
              const Text(
                'Company trips are recorded with their location. Choose "Allow" / "While using the app".',
                textAlign: TextAlign.center,
                style: TextStyle(fontSize: 13, color: OppahTheme.muted),
              ),
              const SizedBox(height: 24),
              SizedBox(height: 50, child: FilledButton(onPressed: _fix, child: const Text('Fix now / Rekebisha'))),
              TextButton(onPressed: _check, child: const Text('Check again')),
            ],
          ),
        ),
      ),
    );
  }
}
