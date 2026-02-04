<?php

namespace App\Http\Controllers\Iam;

use App\Http\Controllers\Controller;
use App\Models\IAM\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function index()
    {
        // Cargar roles para optimizar el conteo en la vista
        $permissions = Permission::with('roles')->orderBy('perm_id')->get();
        return view('iam.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('iam.permissions.nuevo');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => [
                'required',
                'max:100',
                function ($attribute, $value, $fail){
                    $normalized = strtolower(trim($value));
                    $exists = Permission::whereRaw('LOWER(TRIM(code)) = ?', [$normalized])->exists();
                    if ($exists) {
                        $fail('El permiso ya está registrado en el sistema');
                    }
                }
            ],
            'description' => 'nullable'
        ], [
            'code.required' => 'Por favor ingrese el código del permiso',
            'code.max' => 'El código no puede exceder los 100 caracteres'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $datos = [
            'code' => $request->code,
            'description' => $request->description,
            'status' => 'activo' // <--- Por defecto activo
        ];
        
        Permission::create($datos);
        return redirect()->route('permissions.index')->with('message', 'Permiso creado exitosamente');
    }

    public function edit(string $id)
    {
        $permission = Permission::with('roles')->findOrFail($id);
        
        // Calcular posición (opcional, para el modal/vista si se usa)
        $allPerms = Permission::orderBy('perm_id')->pluck('perm_id')->toArray();
        $position = array_search($id, $allPerms) + 1;

        return view('iam.permissions.editar', compact('permission', 'position'));
    }

    public function update(Request $request, string $id)
    {
        $permission = Permission::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'code' => [
                'required',
                'max:100',
                function ($attribute, $value, $fail) use ($id) {
                    $normalized = strtolower(trim($value));
                    $exists = Permission::whereRaw('LOWER(TRIM(code)) = ?', [$normalized])
                        ->where('perm_id', '!=', $id)
                        ->exists();
                    if ($exists) {
                        $fail('El permiso ya está registrado en el sistema');
                    }
                }
            ],
            'description' => 'nullable',
            'status' => 'required|in:activo,inactivo' // <--- Validación de estado
        ], [
            'code.required' => 'Por favor ingrese el código del permiso',
            'status.required' => 'El estado es requerido'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Si intenta inactivar, verificamos si tiene roles asignados
        if ($request->status == 'inactivo' && $permission->status == 'activo') {
            if ($permission->roles()->exists()) {
                return redirect()->back()
                    ->with('error', 'No se puede inactivar el permiso porque está asignado a uno o más roles.')
                    ->withInput();
            }
        }
        
        $datos = [
            'code' => $request->code,
            'description' => $request->description,
            'status' => $request->status
        ];
        
        $permission->update($datos);
        return redirect()->route('permissions.index')->with('message', 'Permiso actualizado exitosamente');
    }

    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);
    
        if ($permission->status == 'activo') {
            // Lógica para Inactivar
            if ($permission->roles()->exists()) {
                return redirect()->route('permissions.index')
                    ->with('error', 'No se puede inactivar el permiso porque está asignado a uno o más roles.');
            }
            
            $permission->update(['status' => 'inactivo']);
            $message = 'Permiso inactivado exitosamente';
        } else {
            // Lógica para Activar
            $permission->update(['status' => 'activo']);
            $message = 'Permiso activado exitosamente';
        }
        
        return redirect()->route('permissions.index')->with('message', $message);
    }
}