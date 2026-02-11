<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SingleSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) return $next($request);

        $userId = $request->user()->id;
        $key = "single_session_user_{$userId}";
        $active = Cache::get($key);

        // si el dueño murió (incógnito cerrado, etc.), liberar
        if ($active && !$this->sessionStillExists($active)) {
            Cache::forget($key);
            $active = null;
        }

        $current = $request->session()->getId();

        if ($active && $active !== $current) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesión cerrada por inicio en otro dispositivo.'], 403);
            }

            return redirect()->route('login')
                ->with('error', 'Tu sesión fue cerrada porque iniciaste sesión en otro dispositivo.');
        }

        Cache::put($key, $current, now()->addMinutes(30));

        return $next($request);
    }

    private function sessionStillExists(string $sessionId): bool
    {
        if (config('session.driver') !== 'file') {
            return true;
        }

        return file_exists(storage_path('framework/sessions/'.$sessionId));
    }
}
