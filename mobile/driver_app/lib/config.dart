/// App-wide settings.
class Config {
  /// The portal the app talks to.
  /// Override for testing: flutter run --dart-define=API_BASE=http://10.0.2.2:8000
  static const String baseUrl = String.fromEnvironment('API_BASE', defaultValue: 'https://www.oppah01.co.tz');

  static const String apiPrefix = '/api/driver/v1';

  /// Background tracking while a trip is active: a point at most every
  /// [trackingInterval], and only after moving [trackingDistanceMeters].
  static const Duration trackingInterval = Duration(seconds: int.fromEnvironment('TRACK_SECONDS', defaultValue: 120));
  static const int trackingDistanceMeters = 100;

  /// How often queued entries and points are pushed when online.
  static const Duration syncInterval = Duration(minutes: 1);
}
