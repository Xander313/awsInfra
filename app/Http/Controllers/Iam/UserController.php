<?php

namespace App\Http\Controllers\Iam;

use App\Http\Controllers\Controller;
use App\Models\IAM\AppUser;
use App\Models\IAM\Role; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = AppUser::with('roles')->orderBy('user_id')->get();
        return view('iam.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('iam.users.nuevo', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación idéntica a la original, sin reglas de 'roles'
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    if (AppUser::where('email', $value)->exists()) {
                        $fail('El email ya está registrado en el sistema');
                    }
                }
            ],
            'status' => 'required|in:activo,suspendido',
            'unit_id' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) {
                    if (!empty($value) && AppUser::where('unit_id', $value)->exists()) {
                        $fail('El ID de unidad ya está registrado en el sistema');
                    }
                }
            ]
        ], [
            'email.required' => 'Por favor ingrese el email del usuario',
            'email.email' => 'Ingrese un email válido',
            'full_name.required' => 'Por favor ingrese el nombre completo',
            'status.required' => 'Por favor seleccione el estado del usuario',
            'status.in' => 'Estado no válido'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $datos = [
            'email' => $request->email,
            'full_name' => $request->full_name,
            'status' => $request->status,
            'unit_id' => $request->unit_id,
            'created_at' => now()
        ];

        $user = AppUser::create($datos);

        // Lógica similar a RoleController::store
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        return redirect()->route('users.index')->with('message', 'Usuario creado exitosamente');
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
        $user = AppUser::with('roles')->findOrFail($id);
        $roles = Role::orderBy('name')->get();
        return view('iam.users.editar', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = AppUser::findOrFail($id);

        // Validación idéntica a la original, sin reglas de 'roles'
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) use ($id) {
                    $exists = AppUser::where('email', $value)
                        ->where('user_id', '!=', $id)
                        ->exists();
                    if ($exists) {
                        $fail('El email ya está registrado en el sistema');
                    }
                }
            ],
            'status' => 'required|in:activo,suspendido',
            'unit_id' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) use ($id) {
                    if (!empty($value)) {
                        $exists = AppUser::where('unit_id', $value)
                            ->where('user_id', '!=', $id)
                            ->exists();
                        if ($exists) {
                            $fail('El ID de unidad ya está registrado en el sistema');
                        }
                    }
                }
            ]
        ], [
            'email.required' => 'Por favor ingrese el email del usuario',
            'email.email' => 'Ingrese un email válido',
            'full_name.required' => 'Por favor ingrese el nombre completo',
            'status.required' => 'Por favor seleccione el estado del usuario',
            'status.in' => 'Estado no válido'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $datos = [
            'email' => $request->email,
            'full_name' => $request->full_name,
            'status' => $request->status,
            'unit_id' => $request->unit_id
        ];

        $user->update($datos);
        
        // Lógica similar a RoleController::update
        $user->roles()->sync($request->roles ?? []);
        
        return redirect()->route('users.index')->with('message', 'Usuario actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = AppUser::findOrFail($id);

        if ($user->status == 'activo') {
            $user->update(['status' => 'suspendido']);
            $message = 'Usuario suspendido exitosamente';
        } else {
            $user->update(['status' => 'activo']);
            $message = 'Usuario activado exitosamente';
        }

        return redirect()->route('users.index')->with('message', $message);
    }
}