<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class AutoBan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->ip() == '91.92.190.63') {
           return $next($request);
        }

        $bannedIps = Cache::get('banned-ips', ['62.212.88.47', '5.182.44.142', '185.244.36.217', '185.142.158.194', '188.229.86.48', '108.181.126.147',
        '94.182.91.226', '188.212.22.252', '185.55.225.6', '176.9.35.126', '93.115.223.250', '88.198.230.56',
        '185.94.98.252', '168.119.213.43', '193.151.150.73', '46.4.97.122', '192.42.116.176', '31.40.216.202']);


        if (in_array($request->ip(), [...$bannedIps])) {
            abort(403, ".|.");
        }

        $ipTries = Cache::get($request->ip() . '-tries', 0);
        Cache::set($request->ip() . '-tries', $ipTries + 1, 600);

        if ($ipTries > 30) {
            Cache::set('banned-ips', [...$bannedIps, $request->ip()]);
        }


        return $next($request);
    }
}
