<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\IAM\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class PasswordResetController extends Controller
{
    public function showResetForm()
    {
        return view('auth.reset', [
            'email' => Session::get('reset_email'),
        ]);
    }

    public function sendResetCode(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = AppUser::where('email', $validated['email'])->first();
        if (!$user) {
            return back()->withErrors(['email' => 'No existe una cuenta con ese correo.'])->withInput();
        }

        $code = (string) random_int(100000, 999999);

        Session::put('reset_email', $user->email);
        Session::put('reset_code_hash', Hash::make($code));
        Session::put('reset_code_expires_at', now()->addMinutes(10)->toDateTimeString());

        try {
            Mail::send('emails.verification_code', [
                'name' => $user->full_name ?? $user->email,
                'code' => $code,
                'minutes' => 10,
            ], function ($msg) use ($user) {
                $msg->to($user->email)->subject('Tu código de verificación');
            });
        } catch (\Throwable $e) {
            Session::forget(['reset_email', 'reset_code_hash', 'reset_code_expires_at']);
            return back()->withErrors(['email' => 'No se pudo enviar el correo. Revisa la configuración SMTP.'])->withInput();
        }

        return redirect()->route('password.reset')
            ->with('success', 'Te enviamos un código a tu correo. Ingresa el código para continuar.');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Session::has('reset_email') || !Session::has('reset_code_hash')) {
            return redirect()->route('password.reset')
                ->withErrors(['code' => 'Tu sesión expiró. Solicita un nuevo código.']);
        }

        $expiresAt = Carbon::parse(Session::get('reset_code_expires_at'));
        if (now()->greaterThan($expiresAt)) {
            Session::forget(['reset_email', 'reset_code_hash', 'reset_code_expires_at']);
            return redirect()->route('password.reset')
                ->withErrors(['code' => 'El código expiró. Solicita uno nuevo.']);
        }

        $ok = Hash::check($request->code, Session::get('reset_code_hash'));
        if (!$ok) {
            return back()->withErrors(['code' => 'Código incorrecto.'])->withInput();
        }

        $email = Session::get('reset_email');
        $user = AppUser::where('email', $email)->first();
        if (!$user) {
            Session::forget(['reset_email', 'reset_code_hash', 'reset_code_expires_at']);
            return redirect()->route('password.reset')
                ->withErrors(['email' => 'No encontramos una cuenta con ese correo.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        Session::forget(['reset_email', 'reset_code_hash', 'reset_code_expires_at']);

        return redirect()->route('login')
            ->with('success', 'Contraseña actualizada. Ya puedes iniciar sesión.');
    }
}
