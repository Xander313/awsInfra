<?php

namespace App\Http\Controllers\Iam;

use App\Http\Controllers\Controller;
use App\Models\IAM\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::orderBy('perm_id')->get();
        return view('iam.permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('iam.permissions.nuevo');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación personalizada para evitar problemas con el esquema
        $validator = Validator::make($request->all(), [
            'code' => [
                'required',
                'max:100',
                function ($attribute, $value, $fail){
                    $normalized = strtolower(trim($value));
                    $exists = Permission::whereRaw('LOWER(TRIM(code)) = ?', [$normalized])
                        ->exists();
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $datos = [
            'code' => $request->code,
            'description' => $request->description
        ];
        
        Permission::create($datos);
        return redirect()->route('permissions.index')->with('message', 'Permiso creado exitosamente');
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
        $permission = Permission::findOrFail($id);
        return view('iam.permissions.editar', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $permission = Permission::findOrFail($id);
        
        // Validación personalizada para evitar problemas con el esquema
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
            'description' => 'nullable'
        ], [
            'code.required' => 'Por favor ingrese el código del permiso',
            'code.max' => 'El código no puede exceder los 100 caracteres'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $datos = [
            'code' => $request->code,
            'description' => $request->description
        ];
        
        $permission->update($datos);
        return redirect()->route('permissions.index')->with('message', 'Permiso actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::findOrFail($id);
    
        // Validar si el permiso está asignado a roles
        if ($permission->roles()->exists()) {
            return redirect()->route('permissions.index')
                ->with('error', 'No se puede eliminar el permiso porque está asignado a roles.');
        }
        
        $permission->delete();
        
        return redirect()->route('permissions.index')
            ->with('message', 'Permiso eliminado exitosamente');
    }
}