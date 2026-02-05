<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // PASO 1: valida + genera código + envía correo + guarda en sesión
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Código de 6 dígitos
        $code = (string) random_int(100000, 999999);

        // Guarda datos "pendientes" (NO guardes password plano)
        Session::put('pending_register', [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Guarda el código hasheado + expiración
        Session::put('register_code_hash', Hash::make($code));
        Session::put('register_code_expires_at', now()->addMinutes(10)->toDateTimeString());

        // Enviar correo
        try {
            Mail::send('emails.verification_code', [
                'name' => $validated['name'],
                'code' => $code,
                'minutes' => 10,
            ], function ($msg) use ($validated) {
                $msg->to($validated['email'])
                    ->subject('Tu código de verificación');
            });
        } catch (\Throwable $e) {
            // limpia sesión si falló el envío
            Session::forget(['pending_register', 'register_code_hash', 'register_code_expires_at']);
            return back()->withErrors(['email' => 'No se pudo enviar el correo. Revisa la configuración SMTP.'])->withInput();
        }

        return redirect()->route('verify_email.form')
            ->with('success', 'Te enviamos un código a tu correo. Ingresa el código para completar el registro.');
    }

    public function showVerifyForm()
    {
        if (!Session::has('pending_register')) {
            return redirect()->route('register')->withErrors(['email' => 'Primero completa el formulario de registro.']);
        }

        return view('auth.verify-email', [
            'email' => Session::get('pending_register.email'),
        ]);
    }

    // PASO 2: valida código + crea usuario + login
    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        if (!Session::has('pending_register') || !Session::has('register_code_hash')) {
            return redirect()->route('register')->withErrors(['code' => 'Tu sesión expiró. Regístrate nuevamente.']);
        }

        // Expiración
        $expiresAt = Carbon::parse(Session::get('register_code_expires_at'));
        if (now()->greaterThan($expiresAt)) {
            Session::forget(['pending_register', 'register_code_hash', 'register_code_expires_at']);
            return redirect()->route('register')->withErrors(['code' => 'El código expiró. Regístrate nuevamente.']);
        }

        // Comparar código
        $ok = Hash::check($request->code, Session::get('register_code_hash'));
        if (!$ok) {
            return back()->withErrors(['code' => 'Código incorrecto.'])->withInput();
        }

        $data = Session::get('pending_register');

        // Re-chequeo de email por si alguien se registró mientras tanto
        if (User::where('email', $data['email'])->exists()) {
            Session::forget(['pending_register', 'register_code_hash', 'register_code_expires_at']);
            return redirect()->route('login')->withErrors(['email' => 'Ese correo ya está registrado. Inicia sesión.']);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'email_verified_at' => now(), // opcional pero recomendado si ya verificaste
        ]);

        Session::forget(['pending_register', 'register_code_hash', 'register_code_expires_at']);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    // OPCIONAL: reenviar (regenera código y vuelve a mandar)
    public function resendCode()
    {
        if (!Session::has('pending_register')) {
            return redirect()->route('register');
        }

        $data = Session::get('pending_register');
        $code = (string) random_int(100000, 999999);

        Session::put('register_code_hash', Hash::make($code));
        Session::put('register_code_expires_at', now()->addMinutes(10)->toDateTimeString());

        Mail::send('emails.verification_code', [
            'name' => $data['name'],
            'code' => $code,
            'minutes' => 10,
        ], function ($msg) use ($data) {
            $msg->to($data['email'])->subject('Tu código de verificación (reenviado)');
        });

        return back()->with('success', 'Te reenviamos un nuevo código.');
    }
}
