import 'dart:convert';

import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'api.dart';
import 'local_db.dart';

/// The logged-in driver: token (encrypted storage) and profile (trucks,
/// expense types), cached so the app also works offline.
class Session {
  static const _storage = FlutterSecureStorage();
  static final Api api = Api();

  static Map<String, dynamic>? profile;

  static List<Map<String, dynamic>> get trucks =>
      List<Map<String, dynamic>>.from((profile?['trucks'] as List?) ?? const []);

  static List<Map<String, dynamic>> get expenseTypes =>
      List<Map<String, dynamic>>.from((profile?['expense_types'] as List?) ?? const []);

  static String get driverName => (profile?['user']?['name'] ?? '').toString();

  /// Restore a saved login. Returns true when a token is available.
  static Future<bool> restore() async {
    api.token = await _storage.read(key: 'token');
    final prefs = await SharedPreferences.getInstance();
    final cached = prefs.getString('profile');
    if (cached != null) profile = jsonDecode(cached);
    return api.token != null;
  }

  static Future<void> login(String email, String password) async {
    final res = await api.post('login', {'email': email, 'password': password, 'device_name': 'Android driver app'});
    api.token = res['token'];
    await _storage.write(key: 'token', value: api.token);
    await _saveProfile(res);
  }

  /// Refresh trucks / expense types when online (ignored when offline).
  static Future<void> refreshProfile() async {
    try {
      await _saveProfile(await api.get('me'));
    } on ApiException catch (e) {
      if (e.unauthorized) rethrow;
    }
  }

  static Future<void> _saveProfile(Map<String, dynamic> res) async {
    profile = {'user': res['user'], 'trucks': res['trucks'], 'expense_types': res['expense_types']};
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('profile', jsonEncode(profile));
  }

  static Future<void> logout() async {
    try {
      await api.post('logout');
    } catch (_) {}
    api.token = null;
    profile = null;
    await _storage.delete(key: 'token');
    final prefs = await SharedPreferences.getInstance();
    await prefs.clear();
    await LocalDb.clearAll();
  }
}
