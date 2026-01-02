<?php

namespace App\Http\Controllers\Iam;

use App\Http\Controllers\Controller;
use App\Models\IAM\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = AppUser::orderBy('user_id')->get();
        return view('iam.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('iam.users.nuevo');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación personalizada para evitar problemas con el esquema
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
            'full_name' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (AppUser::where('full_name', $value)->exists()) {
                        $fail('El nombre completo ya está registrado en el sistema');
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
        
        AppUser::create($datos);
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
        $user = AppUser::findOrFail($id);
        return view('iam.users.editar', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = AppUser::findOrFail($id);
        
        // Validación personalizada para evitar problemas con el esquema
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
            'full_name' => [
                'required',
                function ($attribute, $value, $fail) use ($id) {
                    $exists = AppUser::where('full_name', $value)
                        ->where('user_id', '!=', $id)
                        ->exists();
                    if ($exists) {
                        $fail('El nombre completo ya está registrado en el sistema');
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
        return redirect()->route('users.index')->with('message', 'Usuario actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = AppUser::findOrFail($id);

        // Determinar acción basada en estado actual
        if ($user->status == 'activo') {
            // Suspender usuario activo
            $user->update(['status' => 'suspendido']);
            $message = 'Usuario suspendido exitosamente';
        } else {
            // Activar usuario suspendido
            $user->update(['status' => 'activo']);
            $message = 'Usuario activado exitosamente';
        }
        
        return redirect()->route('users.index')->with('message', $message);
    }
}