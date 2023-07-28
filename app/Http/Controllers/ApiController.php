<?php

namespace App\Http\Controllers;

use App\Models\User;

class ApiController extends Controller
{
    //
    public function authenticate(): \Illuminate\Http\JsonResponse
    {
        $request = request();
        // with apiToken and secretKey should be validate
        $request->validate([
            'api_key' => 'required|string|max:255',
            'secret_key' => 'required|string|max:10'
        ]);

        $requestApiToken = $request->input('api_key');
        $requestSecretKey = $request->input('secret_key');

        // loginAttempt with api_key to token
        $attempt = User::where([
            'api_key' => $requestApiToken,
            'secret_key' => $requestSecretKey
        ])->first();

        if ($attempt) {
            auth()->login($attempt);

            return response()->json(['status' => true, 'message' => 'Authenticated']);
        }
        return response()->json(['status' => false, 'message' => 'Invalid credentials']);
    }

    public function users(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['status' => true, 'message' => 'Authenticated', 'data' => User::all()]);
    }
}
