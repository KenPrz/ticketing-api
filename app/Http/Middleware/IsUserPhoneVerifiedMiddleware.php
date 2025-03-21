<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsUserPhoneVerifiedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->hasVerifiedOtp()) {
            return response()->json([
                'message' => 'Phone verification required.',
                'status' => 'error'
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}