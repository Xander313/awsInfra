<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\Audit\Control;
use App\Models\IAM\AppUser;
use Illuminate\Http\Request;

class ControlController extends Controller
{
    public function index()
    {
        if (!session()->has('org_id')) {
            return redirect()
                ->route('orgs.index')
                ->with('warning', 'Active una organización para visualizar los controles.');
        }

        $controls = Control::where('org_id', session('org_id'))
            ->with('owner', 'org')
            ->orderBy('name')
            ->get();

        return view('audit.controls.index', compact('controls'));
    }

    public function create()
    {
        if (!session()->has('org_id')) {
            return redirect()
                ->route('orgs.index')
                ->with('warning', 'Debe activar una organización antes de registrar controles.');
        }

        $users = AppUser::all();
        $control = null; // ✅ importante: create = null

        return view('audit.controls.create', compact('users', 'control'));
    }

    public function store(Request $request)
    {
        if (!session()->has('org_id')) {
            return redirect()->route('orgs.index')
                ->with('warning', 'Debe activar una organización.');
        }

        $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'control_type' => 'required|string|max:50',
            'description' => 'nullable|string',
            'owner_user_id' => ['nullable', 'exists:' . AppUser::class . ',user_id'],
        ]);

        Control::create([
            'org_id' => session('org_id'),
            'code' => $request->code,
            'name' => $request->name,
            'control_type' => $request->control_type,
            'description' => $request->description,
            'owner_user_id' => $request->owner_user_id,
            'status' => 'Activo',
        ]);

        return redirect()->route('controls.index')
            ->with('success', 'Control creado correctamente.');
    }

    public function show(Control $control)
    {
        $this->authorizeControl($control);

        $control->load(['org', 'owner', 'findings']);

        return view('audit.controls.show', compact('control'));
    }

    // ✅ EDIT usa la misma vista create
    public function edit(Control $control)
    {
        $this->authorizeControl($control);

        $control->load(['org', 'owner']);
        $users = AppUser::all();

        // ✅ misma vista que create
        return view('audit.controls.create', compact('control', 'users'));
    }

    public function update(Request $request, Control $control)
    {
        $this->authorizeControl($control);

        $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'control_type' => 'required|string|max:50',
            'description' => 'nullable|string',
            'owner_user_id' => ['nullable', 'exists:' . AppUser::class . ',user_id'],
            'status' => 'required|string|max:50',
        ]);

        $control->update([
            'code' => $request->code,
            'name' => $request->name,
            'control_type' => $request->control_type,
            'description' => $request->description,
            'owner_user_id' => $request->owner_user_id,
            'status' => $request->status,
        ]);

        return redirect()->route('controls.index')
            ->with('success', 'Control actualizado correctamente.');
    }

    private function authorizeControl(Control $control)
    {
        if (!session()->has('org_id') || (int) $control->org_id !== (int) session('org_id')) {
            abort(403, 'El control no pertenece a la organización activa.');
        }
    }
}
