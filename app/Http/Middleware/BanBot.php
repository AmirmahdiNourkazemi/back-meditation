<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BanBot
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('cf-worker')) {
            return response()->json(['message' => 'unauthorized'], 403);
        }

        if (in_array($request->ip(), ['23.95.132.45'])) {
            return response()->json(['message' => 'unauthorized'], 403);
        }
        return $next($request);
    }
}
