import 'dart:io';

import 'package:flutter/services.dart';
import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:oppah_driver/screens/common.dart';
import 'package:oppah_driver/screens/login_screen.dart';
import 'package:oppah_driver/theme.dart';

// Renders key screens to PNG (test/goldens) to review the design:
//   flutter test --update-goldens test/design_preview_test.dart
void main() {
  // Real fonts (the test renderer otherwise draws text as blocks).
  setUpAll(() async {
    final fonts = '/Applications/flutter/bin/cache/artifacts/material_fonts';
    Future<void> load(String family, List<String> files) async {
      final loader = FontLoader(family);
      for (final f in files) {
        loader.addFont(Future.value(ByteData.view(File('$fonts/$f').readAsBytesSync().buffer)));
      }
      await loader.load();
    }
    await load('Roboto', ['Roboto-Regular.ttf', 'Roboto-Medium.ttf', 'Roboto-Bold.ttf']);
    await load('MaterialIcons', ['MaterialIcons-Regular.otf']);
  });

  Future<void> shoot(WidgetTester tester, Widget home, String name) async {
    tester.view.physicalSize = const Size(1080, 1920);
    tester.view.devicePixelRatio = 2.625;
    await tester.pumpWidget(MaterialApp(debugShowCheckedModeBanner: false, theme: OppahTheme.light, home: home));
    await tester.runAsync(() async {
      for (final e in tester.widgetList<Image>(find.byType(Image))) {
        await precacheImage(e.image, tester.element(find.byWidget(e)));
      }
    });
    await tester.pumpAndSettle();
    await expectLater(find.byType(MaterialApp), matchesGoldenFile('goldens/$name.png'));
  }

  testWidgets('login', (tester) async => shoot(tester, const LoginScreen(), 'login'));

  testWidgets('trips list look', (tester) async => shoot(
        tester,
        Scaffold(
          appBar: AppBar(title: const LogoTitle('My trips / Safari zangu')),
          floatingActionButton: FloatingActionButton.extended(onPressed: () {}, icon: const Icon(Icons.add), label: const Text('New trip')),
          body: ListView(children: [
            Card(
              color: OppahTheme.soft,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              margin: const EdgeInsets.fromLTRB(12, 12, 12, 4),
              child: const Padding(
                padding: EdgeInsets.all(12),
                child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                  Text('Test Driver', style: TextStyle(fontWeight: FontWeight.bold)),
                  Text('Truck: T000TEST'),
                  SizedBox(height: 6),
                  Row(children: [Icon(Icons.my_location, size: 18, color: OppahTheme.blue), SizedBox(width: 6), Expanded(child: Text('Tracking trip OPPA002OCT', style: TextStyle(color: OppahTheme.blue)))]),
                  Row(children: [Icon(Icons.cloud_done, size: 18, color: Colors.green), SizedBox(width: 6), Expanded(child: Text('All sent / Zote zimetumwa'))]),
                ]),
              ),
            ),
            for (final t in [('OPPA002OCT', 'Pending', 'Dar → Mbeya'), ('OPPA001OCT', 'submitted', 'Dar → Morogoro')])
              Card(
                margin: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                child: ListTile(
                  leading: const Icon(Icons.local_shipping, color: OppahTheme.green),
                  title: Text(t.$1),
                  subtitle: Text('2026-10-05 · ${t.$3}'),
                  trailing: statusChip(t.$2),
                ),
              ),
          ]),
        ),
        'trips'));
}
