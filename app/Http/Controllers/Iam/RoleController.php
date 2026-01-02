<?php

namespace App\Http\Controllers\Iam;

use App\Http\Controllers\Controller;
use App\Models\IAM\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// importar modelo de permisos
use App\Models\IAM\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::orderBy('role_id')->get();
        return view('iam.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::orderBy('code')->get();
        return view('iam.roles.nuevo', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación personalizada para evitar problemas con el esquema
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'max:100',
                function ($attribute, $value, $fail){
                    $normalized = strtolower(trim($value));
                    $exists = Role::whereRaw('LOWER(TRIM(name)) = ?', [$normalized])
                        ->exists();
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $datos = [
            'name' => $request->name,
            'description' => $request->description
        ];
        
        $role = Role::create($datos);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('roles.index')->with('message', 'Rol creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::orderBy('code')->get();

        // Calcular el número de posición del rol en la lista actual
        $allRoles = Role::orderBy('role_id')->pluck('role_id')->toArray();
        $position = array_search($id, $allRoles) + 1;
        return view('iam.roles.editar', compact('role', 'permissions', 'position'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);
        
        // Validación personalizada para evitar problemas con el esquema
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
            'description' => 'nullable'
        ], [
            'name.required' => 'Por favor ingrese el nombre del rol',
            'name.max' => 'El nombre no puede exceder los 100 caracteres'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $datos = [
            'name' => $request->name,
            'description' => $request->description
        ];
        
        $role->update($datos);
        // Sincronizar permisos
        $role->permissions()->sync($request->permissions ?? []);
        return redirect()->route('roles.index')->with('message', 'Rol actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
    
        // Validar si el rol tiene permisos asignados
        if ($role->permissions()->exists()) {
            return redirect()->route('roles.index')
                ->with('error', 'No se puede eliminar el rol porque tiene permisos asignados.');
        }
        
        $role->delete();
        
        return redirect()->route('roles.index')
            ->with('message', 'Rol eliminado exitosamente');
    }
}