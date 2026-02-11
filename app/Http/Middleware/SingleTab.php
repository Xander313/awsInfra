<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class SingleTab
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) return $next($request);

        $tabId = $request->header('X-TAB-ID');
        if (!$tabId) return $next($request);

        $sessionId = $request->session()->getId();
        $key = "single_tab_session_{$sessionId}";
        $active = Cache::get($key);

        if ($active && $active !== $tabId) {
            abort(403, 'La aplicación ya está siendo usada por otro usuario.');
        }

        Cache::put($key, $tabId, now()->addMinutes(30));

        return $next($request);
    }
}
