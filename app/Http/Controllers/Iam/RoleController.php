<?php

namespace App\Http\Controllers\Iam;

use App\Http\Controllers\Controller;
use App\Models\IAM\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\IAM\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('role_id')->get();
        return view('iam.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('code')->get();
        return view('iam.roles.nuevo', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'max:100',
                function ($attribute, $value, $fail){
                    $normalized = strtolower(trim($value));
                    $exists = Role::whereRaw('LOWER(TRIM(name)) = ?', [$normalized])->exists();
                    if ($exists) {
                        $fail('El rol ya está registrado en el sistema');
                    }
                }
            ],
            'description' => 'nullable'
        ], [
            'name.required' => 'Por favor ingrese el nombre del rol',
            'name.max' => 'El nombre no puede exceder los 100 caracteres'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $datos = [
            'name' => $request->name,
            'description' => $request->description,
            'status' => 'activo' // <--- Por defecto activo
        ];
        
        $role = Role::create($datos);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('roles.index')->with('message', 'Rol creado exitosamente');
    }

    public function edit(string $id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::orderBy('code')->get();

        $allRoles = Role::orderBy('role_id')->pluck('role_id')->toArray();
        $position = array_search($id, $allRoles) + 1;
        return view('iam.roles.editar', compact('role', 'permissions', 'position'));
    }

    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'max:100',
                function ($attribute, $value, $fail) use ($id) {
                    $normalized = strtolower(trim($value));
                    $exists = Role::whereRaw('LOWER(TRIM(name)) = ?', [$normalized])
                        ->where('role_id', '!=', $id)
                        ->exists();
                    if ($exists) {
                        $fail('El rol ya está registrado en el sistema');
                    }
                }
            ],
            'description' => 'nullable',
            'status' => 'required|in:activo,inactivo' // <--- Validación de estado
        ], [
            'name.required' => 'Por favor ingrese el nombre del rol',
            'status.required' => 'El estado es requerido'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Si intenta inactivar, verificamos si tiene usuarios
        if ($request->status == 'inactivo' && $role->status == 'activo') {
            if ($role->users()->exists()) {
                return redirect()->back()
                    ->with('error', 'No se puede inactivar el rol porque tiene usuarios asignados.')
                    ->withInput();
            }
        }

        $datos = [
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status
        ];
        
        $role->update($datos);
        $role->permissions()->sync($request->permissions ?? []);
        
        return redirect()->route('roles.index')->with('message', 'Rol actualizado exitosamente');
    }

    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
    
        if ($role->status == 'activo') {
            // Lógica para Inactivar
            // Validar si el rol tiene USUARIOS asignados
            if ($role->users()->exists()) {
                return redirect()->route('roles.index')
                    ->with('error', 'No se puede inactivar el rol porque tiene usuarios asignados.');
            }

            $role->update(['status' => 'inactivo']);
            $message = 'Rol inactivado exitosamente';
        } else {
            // Lógica para Activar
            $role->update(['status' => 'activo']);
            $message = 'Rol activado exitosamente';
        }
        
        return redirect()->route('roles.index')->with('message', $message);
    }
}