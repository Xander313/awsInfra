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

            // --- NUEVO BLOQUE DE VALIDACIÓN DE ESTADO ---
            $user = Auth::user();
            
            // Si está suspendido y NO es ADMIN_SISTEMA (protección doble)
            if ($user->status == 'suspendido') {
                if (!$user->roles()->where('name', 'ADMIN_SISTEMA')->exists()) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    return back()->withErrors([
                        'email' => 'Su cuenta ha sido suspendida. Comuníquese con el administrador.',
                    ])->onlyInput('email');
                }
            }



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



    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
}

}
