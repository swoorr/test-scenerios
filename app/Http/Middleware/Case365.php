<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Case365
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // get api_key and secret_key from header
        $apiKey = $request->header('api_key');
        $secretKey = $request->header('secret_key');

        // check if api_key and secret_key are not empty
        if(empty($apiKey) || empty($secretKey))
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);

        // check if api_key and secret_key are valid from User Model
        if(!User::where('api_key', $apiKey)->where('secret_key', $secretKey)->exists())
            return response()->json(['status' => false, 'message' => '! Unauthorized'], 401);

        // set auth user
        auth()->setUser(User::where('api_key', $apiKey)->where('secret_key', $secretKey)->first());

        return $next($request);
    }

    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
}
