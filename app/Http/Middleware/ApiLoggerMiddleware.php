<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiLog;

class ApiLoggerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // First run the request
        $response = $next($request);

        // Create Log
        ApiLog::create([
            'client'           => $request->header('client', 'Unknown'),
            'service'          => $request->path(),
            'vendor'           => $request->header('vendor', 'Unknown'),
            'status'           => $response->status(),
            'request_payload'  => $request->all(),
            'response_payload' => json_decode($response->getContent(), true),
        ]);

        return $response;
    }
}
