<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyDeviceToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization', '');
        $token = str_starts_with($authHeader, 'Bearer ')
            ? substr($authHeader, 7)
            : null;

        $expected = config('services.device.api_token');

        if (!$token || !$expected || !hash_equals($expected, $token)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized device.',
            ], 401);
        }

        return $next($request);
    }
}