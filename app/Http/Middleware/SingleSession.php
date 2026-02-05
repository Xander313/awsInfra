<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class SingleSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) return $next($request);

        $key = 'single_session_active';
        $active = Cache::get($key);

        // si el dueño murió (incógnito cerrado, etc.), liberar
        if ($active && !$this->sessionStillExists($active)) {
            Cache::forget($key);
            $active = null;
        }

        $current = $request->session()->getId();

        if ($active && $active !== $current) {
            abort(403, 'La aplicación ya está siendo usada por otro usuario.');
        }

        Cache::put($key, $current, now()->addMinutes(30));

        return $next($request);
    }

    private function sessionStillExists(string $sessionId): bool
    {
        // SOLO si SESSION_DRIVER=file
        return file_exists(storage_path('framework/sessions/'.$sessionId));
    }
}
