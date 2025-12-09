<?php

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use App\Models\ClientConfiguration;

// class ApiAuthMiddleware
// {
//     public function handle(Request $request, Closure $next)
//     {
//         $apiKey = $request->header('X-API-KEY');
//         $secret = $request->header('X-SECRET-KEY');
//         $ip = $request->ip();

//         // Validate client
//         $client = ClientConfiguration::where('client_api_key', $apiKey)
//                   ->where('client_secret_key', $secret)
//                   ->where('is_active', true)
//                   ->firstOrFail();

//         // IP whitelist check
//         if ($client->allowed_ips && !in_array($ip, $client->allowed_ips)) {
//             return response()->json(['error'=>'IP not allowed'], 403);
//         }

//         // TPS rate limit (optional, Redis/Counters)
//         // Example: $client->tps;

//         // Pass client instance to request
//         $request->client = $client;

//         return $next($request);
//     }
// }
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ClientConfiguration;

class ApiAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->client_api_key;
        $secret = $request->client_secret;

        if (!$apiKey || !$secret) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $client = ClientConfiguration::where('client_api_key', $apiKey)
            ->where('client_secret_key', $secret)
            ->where('is_active', true)
            ->first();

        if (!$client) {
            return response()->json(['error' => 'Invalid API Credentials'], 403);
        }

        return $next($request);
    }
}

