import 'dart:io';
import 'package:http/http.dart' as http;
import 'package:http/io_client.dart';
import '../config.dart';

http.Client buildClient() {
  final hc = HttpClient()
    ..badCertificateCallback = (cert, host, port) => host == serverHost;
  return IOClient(hc);
}