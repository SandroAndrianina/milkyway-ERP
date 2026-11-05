import 'package:flutter/material.dart';
import 'config.dart';
import 'services/api_client.dart';

void main() => runApp(const MyApp());

class MyApp extends StatefulWidget {
  const MyApp({super.key});
  @override
  State<MyApp> createState() => _MyAppState();
}

class _MyAppState extends State<MyApp> {
  String result = 'Testing...';

  @override
  void initState() {
    super.initState();
    test();
  }

  Future<void> test() async {
    try {
      final r = await buildClient()
          .get(Uri.parse('$baseUrl/api/health'))
          .timeout(const Duration(seconds: 2));
      setState(() => result = 'Status: ${r.statusCode}');
    } catch (e) {
      setState(() => result = 'Error: $e');
    }
  }

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      home: Scaffold(body: Center(child: Text(result))),
    );
  }
}