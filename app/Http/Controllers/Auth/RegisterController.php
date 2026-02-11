<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\IAM\AppUser; // <--- Usamos el modelo correcto
use App\Models\IAM\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule; // <--- Necesario para validar listas

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // PASO 1: valida + genera código + envía correo + guarda en sesión
    public function register(Request $request)
    {
        // Cargar ubicaciones desde la configuración
        $ubicaciones = config('ecuador.ubicaciones');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // FIX: Agregamos 'pgsql.' al inicio para que detecte la conexión correcta
            // y no confunda el esquema 'iam' con una conexión.
            'email' => ['required', 'string', 'email', 'max:255', 'unique:pgsql.iam.app_user,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            
            // Validaciones de Provincia y Cantón
            'provincia' => ['required', Rule::in(array_keys($ubicaciones))],
            'canton' => [
                'required',
                function ($attribute, $value, $fail) use ($request, $ubicaciones) {
                    $provincia = $request->provincia;
                    // Validar que el cantón pertenezca a la provincia seleccionada
                    if (isset($ubicaciones[$provincia]) && !in_array($value, $ubicaciones[$provincia])) {
                        $fail("El cantón seleccionado no es válido para la provincia de $provincia.");
                    }
                },
            ],
        ], [
            'email.unique' => 'Este correo ya está registrado en el sistema.',
            'provincia.required' => 'Seleccione una provincia.',
            'canton.required' => 'Seleccione un cantón.'
        ]);

        // Código de 6 dígitos
        $code = (string) random_int(100000, 999999);

        // Guarda datos "pendientes" (incluyendo ubicación)
        Session::put('pending_register', [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'provincia' => $validated['provincia'],
            'canton' => $validated['canton'],
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
        // Usamos también 'pgsql.iam.app_user' o el modelo AppUser
        if (AppUser::where('email', $data['email'])->exists()) {
            Session::forget(['pending_register', 'register_code_hash', 'register_code_expires_at']);
            return redirect()->route('login')->withErrors(['email' => 'Ese correo ya está registrado. Inicia sesión.']);
        }

        // Crear en AppUser
        $user = AppUser::create([
            'full_name' => $data['name'], // Mapeamos 'name' del form a 'full_name' de la tabla
            'email' => $data['email'],
            'password' => $data['password'],
            'provincia' => $data['provincia'],
            'canton' => $data['canton'],
            'status' => 'activo', // Estado por defecto
            'created_at' => now(),
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