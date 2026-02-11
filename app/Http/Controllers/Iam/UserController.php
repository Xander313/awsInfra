<?php

namespace App\Http\Controllers\Iam;

use App\Http\Controllers\Controller;
use App\Models\IAM\AppUser;
use App\Models\IAM\Role;
use App\Models\IAM\UserRoleHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        $users = AppUser::with('roles')->orderBy('user_id')->get();
        return view('iam.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
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
            'status.required' => 'Por favor seleccione el estado del usuario',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = AppUser::create([
            'email' => $request->email,
            'full_name' => $request->full_name,
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

        return redirect()->route('users.index')->with('message', 'Usuario creado exitosamente');
    }

    public function edit(string $id)
    {
        $user = AppUser::with('roles')->findOrFail($id);
        $roles = Role::orderBy('name')->get();
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
            'status.required' => 'Por favor seleccione el estado del usuario',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->update($request->only(['email', 'full_name', 'status', 'unit_id', 'provincia', 'canton']));

        $rolesToSync = $request->filled('role_id') ? [$request->role_id] : [];
        $oldRoles = $user->roles()->pluck('iam.user_role.role_id')->toArray();

        // Verificar si hubo cambios reales en el rol
        if ($rolesToSync !== $oldRoles) {
            UserRoleHistory::create([
                'user_id' => $user->user_id,
                'role_id' => $request->role_id ?: null, // Si está vacío guarda null
                'action' => 'assigned',
                'assigned_by' => auth()->id() ?? null,
                'created_at' => now()
            ]);
            $user->roles()->sync($rolesToSync);
        }
        
        return redirect()->route('users.index')->with('message', 'Usuario actualizado exitosamente');
    }

    public function destroy(string $id)
    {
        $user = AppUser::findOrFail($id);
        $user->status == 'activo' ? $user->update(['status' => 'suspendido']) : $user->update(['status' => 'activo']);
        return redirect()->route('users.index')->with('message', 'Estado actualizado');
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
}