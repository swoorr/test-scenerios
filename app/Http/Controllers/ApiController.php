<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

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
            return new JsonResponse(['message' => 'Authenticated']);
        }

        return new JsonResponse(['message' => 'Unauthenticated'], 401);
    }

    public function users(): \Illuminate\Http\JsonResponse
    {
        return new JsonResponse(['message' => 'Authenticated', 'data' => User::all()]);
    }
}
