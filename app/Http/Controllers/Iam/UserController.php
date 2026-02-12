<?php

namespace App\Http\Controllers\Iam;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Models\IAM\AppUser;
use App\Models\IAM\Role;
use App\Models\IAM\UserRoleHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = auth()->user();
                
                // Verificamos rol (agregué validación si $user es null por seguridad)
                if (!$user || !$user->roles()->where('name', 'ADMIN_SISTEMA')->exists()) {
                    abort(403, 'No tienes permiso para realizar esta acción.');
                }
                
                return $next($request);
            }, only: ['create', 'store', 'edit', 'update', 'destroy', 'verifyCedula']),
        ];
    }

    public function revealPassword(Request $request, $token)
    {
        
        $password = Cache::pull('temp_password_' . $token);

        if (!$password) {
            abort(403, 'El enlace ha expirado o ya ha sido utilizado.');
        }

        return view('iam.users.revelar', compact('password'));
    }
    
    public function index()
    {
        $users = AppUser::with('roles')->orderBy('user_id')->get();
        return view('iam.users.index', compact('users'));
    }

    public function create()
    {
        $adminRoleName = 'ADMIN_SISTEMA';

        $roles = Role::where('name', '!=', $adminRoleName)->orderBy('name')->get();
        return view('iam.users.nuevo', compact('roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required', 'email',
                function ($attribute, $value, $fail) {
                    if (AppUser::where('email', $value)->exists()) {
                        $fail('El email ya está registrado en el sistema');
                    }
                }
            ],
            'cedula' => [
                'required',
                'digits:10',
                function ($attribute, $value, $fail) {
                    if (!AppUser::query()->where('cedula', $value)->doesntExist()) {
                        $fail('La cédula ya está registrada en el sistema');
                    }

                    if (!$this->isValidEcuadorCedula($value)) {
                        $fail('La cédula ecuatoriana no es válida');
                    }
                }
            ],
            'full_name' => 'required|max:255',
            'password' => 'required|min:8|confirmed',
            'status' => 'required|in:activo,suspendido',
            'unit_id' => [
                'nullable', 'integer',
                function ($attribute, $value, $fail) {
                    if (!empty($value) && AppUser::where('unit_id', $value)->exists()) {
                        $fail('El ID de unidad ya está registrado en el sistema');
                    }
                }
            ],
            'role_id' => [
                'nullable', 
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $exists = DB::table('iam.user_role')->where('role_id', $value)->exists();
                        if ($exists) { $fail('Este rol ya está asignado a otro usuario.'); }
                    }
                }
            ]
        ], [
            'email.required' => 'Por favor ingrese el email del usuario',
            'email.email' => 'Ingrese un email válido',
            'cedula.required' => 'Por favor ingrese la cédula',
            'cedula.digits' => 'La cédula debe contener exactamente 10 dígitos',
            'full_name.required' => 'Por favor ingrese el nombre completo',
            'password.required' => 'La contraseña es obligatoria para nuevos usuarios',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'full_name.max' => 'Limite de caracteres excedido (255)',
            'status.required' => 'Por favor seleccione el estado del usuario',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = AppUser::create([
            'email' => $request->email,
            'cedula' => $request->cedula,
            'full_name' => $request->full_name,
            'password' => $request->password,
            'status' => $request->status,
            'unit_id' => $request->unit_id,
            'provincia' => $request->provincia,
            'canton' => $request->canton,
            'created_at' => now()
        ]);

        if ($request->filled('role_id')) {
            $user->roles()->sync([$request->role_id]);
            UserRoleHistory::create([
                'user_id' => $user->user_id,
                'role_id' => $request->role_id,
                'action' => 'assigned',
                'assigned_by' => auth()->id() ?? null,
                'created_at' => now()
            ]);
        }
        
        // Generar token y guardar en Cache por 20 minutos
        $token = Str::random(40);
        Cache::put('temp_password_' . $token, $request->password, now()->addMinutes(20));

        // Crear URL firmada temporal
        $revealUrl = URL::temporarySignedRoute(
            'users.reveal_password', 
            now()->addMinutes(20), 
            ['token' => $token]
        );

        try {
            Mail::send('emails.credenciales', [
                'user' => $user,
                'revealUrl' => $revealUrl, // Enviamos la URL en lugar de la clave plana
                'tipo' => 'bienvenida'
            ], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Acceso a sus credenciales - SGPD COAC');
            });
        } catch (\Exception $e) { }

        return redirect()->route('users.index')->with('message', 'Usuario creado exitosamente');
    }

    public function edit(string $id)
    {
        $user = AppUser::with('roles')->findOrFail($id);
        
        $adminRoleName = 'ADMIN_SISTEMA';
        $roles = Role::where('name', '!=', $adminRoleName)->orderBy('name')->get();
        return view('iam.users.editar', compact('user', 'roles'));
    }

    public function update(Request $request, string $id)
    {
        $user = AppUser::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'email' => [
                'required', 'email',
                function ($attribute, $value, $fail) use ($id) {
                    $exists = AppUser::where('email', $value)->where('user_id', '!=', $id)->exists();
                    if ($exists) { $fail('El email ya está registrado en el sistema'); }
                }
            ],
            'cedula' => [
                'required',
                'digits:10',
                function ($attribute, $value, $fail) use ($id) {
                    $exists = AppUser::query()
                        ->where('cedula', $value)
                        ->where('user_id', '!=', $id)
                        ->exists();

                    if ($exists) {
                        $fail('La cédula ya está registrada en el sistema');
                    }

                    if (!$this->isValidEcuadorCedula($value)) {
                        $fail('La cédula ecuatoriana no es válida');
                    }
                }
            ],
            'full_name' => 'required|max:255',
            'password' => 'nullable|min:8|confirmed',
            'status' => 'required|in:activo,suspendido',
            'unit_id' => [
                'nullable', 'integer',
                function ($attribute, $value, $fail) use ($id) {
                    if (!empty($value)) {
                        $exists = AppUser::where('unit_id', $value)->where('user_id', '!=', $id)->exists();
                        if ($exists) { $fail('El ID de unidad ya está registrado en el sistema'); }
                    }
                }
            ],
            'role_id' => [
                'nullable',
                function ($attribute, $value, $fail) use ($id) {
                    if (!empty($value)) {
                        $exists = DB::table('iam.user_role')->where('role_id', $value)->where('user_id', '!=', $id)->exists();
                        if ($exists) { $fail('Este rol ya lo tiene otro usuario asignado.'); }
                    }
                }
            ]
        ], [
            'email.required' => 'Por favor ingrese el email del usuario',
            'email.email' => 'Ingrese un email válido',
            'cedula.required' => 'Por favor ingrese la cédula',
            'cedula.digits' => 'La cédula debe contener exactamente 10 dígitos',
            'full_name.required' => 'Por favor ingrese el nombre completo',
            'full_name.max' => 'Limite de caracteres excedido (255)',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'status.required' => 'Por favor seleccione el estado del usuario',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $dataToUpdate = $request->only(['email', 'cedula', 'full_name', 'status', 'unit_id', 'provincia', 'canton']);
        // Si intentan poner 'suspendido' a un ADMIN_SISTEMA, lo impedimos
        if (isset($dataToUpdate['status']) && $dataToUpdate['status'] == 'suspendido') {
            if ($user->roles()->where('name', 'ADMIN_SISTEMA')->exists()) {
                return redirect()->back()->withInput()->with('error', 'No se puede suspender la cuenta del Administrador del Sistema.');
            }
        }
        // NUEVO: Lógica condicional para la contraseña
        $passwordChanged = false;
        if ($request->filled('password')) {
            $dataToUpdate['password'] = $request->password; // El modelo lo hasheará
            $passwordChanged = true;
        }
        $user->update($dataToUpdate);
        $isTargetAdmin = $user->roles()->where('name', 'ADMIN_SISTEMA')->exists();

        $rolesToSync = $request->filled('role_id') ? [$request->role_id] : [];
        $oldRoles = $user->roles()->pluck('iam.user_role.role_id')->toArray();

        if (!$isTargetAdmin) {
            $rolesToSync = $request->filled('role_id') ? [$request->role_id] : [];
            $oldRoles = $user->roles()->pluck('iam.user_role.role_id')->toArray();

            // Verificar si hubo cambios reales en el rol
            if ($rolesToSync !== $oldRoles) {
                UserRoleHistory::create([
                    'user_id' => $user->user_id,
                    'role_id' => $request->role_id ?: null,
                    'action' => 'assigned',
                    'assigned_by' => auth()->id() ?? null,
                    'created_at' => now()
                ]);
                $user->roles()->sync($rolesToSync);
            }
        }

        if ($passwordChanged) {
            $token = Str::random(40);
            Cache::put('temp_password_' . $token, $request->password, now()->addMinutes(20));

            $revealUrl = URL::temporarySignedRoute(
                'users.reveal_password', 
                now()->addMinutes(20), 
                ['token' => $token]
            );

            try {
                Mail::send('emails.credenciales', [
                    'user' => $user,
                    'revealUrl' => $revealUrl,
                    'tipo' => 'actualizacion'
                ], function ($message) use ($user) {
                    $message->to($user->email)
                            ->subject('Actualización de credenciales - SGPD');
                });
            } catch (\Exception $e) { }
        }
        
        return redirect()->route('users.index')->with('message', 'Usuario actualizado exitosamente');
    }

    public function destroy(string $id)
    {
        $user = AppUser::findOrFail($id);

        if ($user->status == 'activo') {
            // --- VALIDACIÓN DE SEGURIDAD ---
            if ($user->roles()->where('name', 'ADMIN_SISTEMA')->exists()) {
                return redirect()->route('users.index')->with('error', 'No se puede suspender la cuenta del Administrador del Sistema.');
            }
            
            $user->update(['status' => 'suspendido']);
            $message = 'Usuario suspendido exitosamente';
        } else {
            $user->update(['status' => 'activo']);
            $message = 'Usuario activado exitosamente';
        }

        return redirect()->route('users.index')->with('message', $message);
    }

    public function verifyCedula(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cedula' => 'required|digits:10',
        ], [
            'cedula.required' => 'Debe enviar una cédula.',
            'cedula.digits' => 'La cédula debe contener exactamente 10 dígitos.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => $validator->errors()->first('cedula'),
            ], 422);
        }

        $cedula = $request->input('cedula');
        if (!$this->isValidEcuadorCedula($cedula)) {
            return response()->json([
                'ok' => false,
                'message' => 'La cédula ecuatoriana no es válida.',
            ], 422);
        }

        $cacheKey = "webservicesec:cedula:{$cedula}";
        if (Cache::has($cacheKey)) {
            return response()->json([
                'ok' => true,
                'cached' => true,
                'data' => Cache::get($cacheKey),
            ]);
        }

        $token = config('services.webservicesec.token');
        $baseUrl = rtrim(config('services.webservicesec.base_url', 'https://webservices.ec/api'), '/');

        if (empty($token)) {
            Log::warning('WEBSERVICESEC_TOKEN no configurado para verificación de cédula');
            return response()->json([
                'ok' => false,
                'message' => 'No se pudo verificar la cédula en este momento.',
            ], 500);
        }

        try {
            $response = Http::acceptJson()
                ->withToken($token)
                ->timeout(15)
                ->get("{$baseUrl}/cedula/{$cedula}");
        } catch (\Throwable $e) {
            Log::error('Error al consultar cédula en webservices.ec', [
                'cedula' => $cedula,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'No se pudo consultar el servicio de cédula.',
            ], 502);
        }

        if (!$response->successful()) {
            Log::warning('Respuesta no exitosa al consultar cédula', [
                'cedula' => $cedula,
                'status' => $response->status(),
                'api_message' => data_get($response->json(), 'message'),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'No fue posible obtener datos para esa cédula.',
            ], 422);
        }

        $apiData = data_get($response->json(), 'data.response', []);
        $nombreCompleto = trim((string) data_get($apiData, 'nombreCompleto', ''));
        $nombres = trim((string) data_get($apiData, 'nombres', ''));
        $apellidos = trim((string) data_get($apiData, 'apellidos', ''));
        $fullNameOrdered = trim("{$nombres} {$apellidos}");

        if ($nombreCompleto === '' && $nombres === '' && $apellidos === '') {
            return response()->json([
                'ok' => false,
                'message' => 'La cédula no devolvió nombres válidos.',
            ], 422);
        }

        $result = [
            'cedula' => (string) data_get($apiData, 'identificacion', $cedula),
            'full_name' => $fullNameOrdered !== '' ? $fullNameOrdered : $nombreCompleto,
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'estado' => (string) data_get($apiData, 'estado', ''),
        ];

        Cache::put($cacheKey, $result, now()->addHours(24));

        return response()->json([
            'ok' => true,
            'cached' => false,
            'data' => $result,
        ]);
    }

    public function rolesHistory(Request $request, string $id)
    {
        $user = AppUser::findOrFail($id);
        $perPage = $request->get('per_page', 10);
        $history = UserRoleHistory::where('user_id', $id)
            ->with(['role', 'assignedBy'])->orderByDesc('created_at')->orderByDesc('id')
            ->paginate($perPage)->withQueryString();

        return view('iam.users.historial_roles', compact('user', 'history'));
    }

    private function isValidEcuadorCedula(?string $cedula): bool
    {
        if (!is_string($cedula) || !preg_match('/^\d{10}$/', $cedula)) {
            return false;
        }

        $provinceCode = (int) substr($cedula, 0, 2);
        $thirdDigit = (int) $cedula[2];

        if ($provinceCode < 1 || $provinceCode > 24 || $thirdDigit >= 6) {
            return false;
        }

        $sum = 0;
        $coefficients = [2, 1, 2, 1, 2, 1, 2, 1, 2];

        for ($i = 0; $i < 9; $i++) {
            $value = ((int) $cedula[$i]) * $coefficients[$i];
            $sum += $value > 9 ? $value - 9 : $value;
        }

        $checkDigit = (10 - ($sum % 10)) % 10;
        return $checkDigit === (int) $cedula[9];
    }
}
