import 'dart:async';
import 'dart:convert';
import 'dart:io';

import 'package:http/http.dart' as http;

import 'config.dart';

/// Error from the portal API. [network] is true when the phone could not
/// reach the server (offline / timeout) — the request should be retried.
class ApiException implements Exception {
  final String message;
  final int? status;
  final bool network;

  ApiException(this.message, {this.status, this.network = false});

  bool get unauthorized => status == 401;

  @override
  String toString() => message;
}

class Api {
  String? token;

  Api({this.token});

  Uri _uri(String path) => Uri.parse('${Config.baseUrl}${Config.apiPrefix}/$path');

  Map<String, String> get _headers => {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        if (token != null) 'Authorization': 'Bearer $token',
      };

  Future<Map<String, dynamic>> get(String path) => _send(() => http.get(_uri(path), headers: _headers));

  Future<Map<String, dynamic>> post(String path, [Map<String, dynamic>? body]) =>
      _send(() => http.post(_uri(path), headers: _headers, body: jsonEncode(body ?? {})));

  Future<Map<String, dynamic>> _send(Future<http.Response> Function() call) async {
    http.Response res;
    try {
      res = await call().timeout(const Duration(seconds: 30));
    } on SocketException {
      throw ApiException('No internet connection. / Hakuna mtandao.', network: true);
    } on TimeoutException {
      throw ApiException('The server took too long. / Seva imechelewa.', network: true);
    } on http.ClientException {
      throw ApiException('No internet connection. / Hakuna mtandao.', network: true);
    }

    Map<String, dynamic> body = {};
    try {
      final decoded = jsonDecode(res.body);
      if (decoded is Map<String, dynamic>) body = decoded;
    } catch (_) {}

    if (res.statusCode >= 200 && res.statusCode < 300) return body;

    throw ApiException(_message(body, res.statusCode), status: res.statusCode, network: res.statusCode >= 500);
  }

  String _message(Map<String, dynamic> body, int status) {
    final errors = body['errors'];
    if (errors is Map && errors.isNotEmpty) {
      final first = errors.values.first;
      if (first is List && first.isNotEmpty) return first.first.toString();
    }
    if (body['message'] is String && (body['message'] as String).isNotEmpty) return body['message'];
    if (status == 401) return 'Please log in again. / Tafadhali ingia tena.';
    if (status == 403) return 'Not allowed. / Hairuhusiwi.';
    return 'Server error ($status). / Hitilafu ya seva.';
  }
}
