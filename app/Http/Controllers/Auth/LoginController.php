<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Cache;



class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $userId = Auth::id();
            $current = $request->session()->getId();
            $key = "single_session_user_{$userId}";
            $active = Cache::get($key);

            if ($active && config('session.driver') === 'file') {
                $path = storage_path('framework/sessions/'.$active);
                if (!file_exists($path)) {
                    Cache::forget($key);
                    $active = null;
                }
            }

            if ($active && $active !== $current) {
                $request->session()->put('session_conflict_active', $active);
                return redirect()->route('session.conflict');
            }

            Cache::put($key, $current, now()->addMinutes(30));

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no son válidas.',
        ])->onlyInput('email');
    }


public function logout(Request $request)
{
    $uid = Auth::id();
    $current = $request->session()->getId();

    $key = "single_session_user_{$uid}";
    $active = Cache::get($key);
    if ($active && $active === $current) {
        Cache::forget($key);
        Cache::forget('single_tab_session_'.$current);
    }

    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
}

}
