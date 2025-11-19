import 'dart:convert';
import 'package:http/http.dart' as http;
import '../variables.dart';
import 'package:shared_preferences/shared_preferences.dart';

class HttpHelper {
  static Future<http.Response> post(
    String endpoint,
    Map<String, dynamic> data, {
    String? token,
  }) async {
    return await http.post(
      Uri.parse('$baseUrl$endpoint'),

      headers: {
        "Content-Type": "application/json",

        "Accept": "application/json",

        if (token != null) "Authorization": "Bearer $token",
      },

      body: jsonEncode(data),
    );
  }

  static Future<http.Response> get(String endpoint, {String? token}) async {
    return await http.get(
      Uri.parse('$baseUrl$endpoint'),

      headers: {
        "Accept": "application/json",

        if (token != null) "Authorization": "Bearer $token",
      },
    );
  }

  // static Future<http.Response> put(
  //   String endpoint,

  //   Map<String, dynamic> data, {

  //   String? token,
  // }) async {
  //   return await http.put(
  //     Uri.parse('$baseUrl$endpoint'),

  //     headers: {
  //       "Content-Type": "application/json",

  //       "Accept": "application/json",

  //       if (token != null) "Authorization": "Bearer $token",
  //     },

  //     body: jsonEncode(data),
  //   );
  // }

  // static Future<http.Response> delete(String endpoint, {String? token}) async {

  // return await http.delete(

  //   Uri.parse('$baseUrl$endpoint'),

  //   headers: {if (token != null) "Authorization": "Bearer $token"},

  // );

  // }
}

class AuthService {
  static Future<bool> login(String email, String password) async {
    final response = await HttpHelper.post('/login', {
      "email": email,
      "password": password,
    });

    if (response.statusCode == 200) {
      String token = jsonDecode(response.body)["token"];

      SharedPreferences prefs = await SharedPreferences.getInstance();

      await prefs.setString('token', token);

      return true;
    } else {
      print('Login failed: ${response.statusCode} - ${response.body}');
      return false;
    }
  }

  static Future<bool> register(
    String name,
    String email,
    String password,
    String confirmPassword,
    String program,
  ) async {
    final response = await HttpHelper.post('/register', {
      "name": name,
      "email": email,
      "password": password,
      "password_confirmation": confirmPassword,
      "program": program,
    });

    print('Register: ${response.statusCode} - ${response.body}');

    if (response.statusCode >= 200 && response.statusCode < 300) {
      var data = jsonDecode(response.body);

      if (data.containsKey("token")) {
        SharedPreferences prefs = await SharedPreferences.getInstance();

        await prefs.setString('token', data["token"]);
      }

      return true;
    } else {
      print('Register faied: ${response.statusCode} - ${response.body}');

      return false;
    }
  }
}
