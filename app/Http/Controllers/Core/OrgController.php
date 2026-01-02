<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\Org;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrgController extends Controller
{
    /**
     * Mostrar todas las organizaciones
     */
    public function index()
    {
        $orgs = Org::orderBy('name')->get();
        $activeOrgId = session('org_id'); // organización activa en sesión
        return view('core.org.index', compact('orgs', 'activeOrgId'));
    }

    /**
     * Formulario para crear nueva organización
     */
    public function create()
    {
        return view('core.org.create');
    }

    /**
     * Guardar nueva organización
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ruc' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique(Org::class, 'ruc'),
            ],
            'industry' => 'nullable|string|max:255',
        ]);

        $org = Org::create($request->only('name', 'ruc', 'industry'));

        // Activar la nueva organización automáticamente
        $this->setActiveOrg($org->org_id);

        return redirect()
            ->route('orgs.index')
            ->with('success', 'Organización creada y activada.');
    }

    /**
     * Mostrar detalles de una organización
     */
    public function show(Org $org)
    {
        return view('core.org.show', compact('org'));
    }

    /**
     * Formulario para editar organización
     */
    public function edit(Org $org)
    {
        return view('core.org.edit', compact('org'));
    }

    /**
     * Actualizar datos de una organización
     */
    public function update(Request $request, Org $org)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ruc' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique(Org::class, 'ruc')->ignore($org->org_id, 'org_id'),
            ],
            'industry' => 'nullable|string|max:255',
        ]);

        $org->update($request->only('name', 'ruc', 'industry'));

        return redirect()
            ->route('orgs.index')
            ->with('success', 'Organización actualizada correctamente.');
    }

    /**
     * Eliminar organización
     */
    public function destroy(Org $org)
    {
        // Evita borrar la organización activa
        if (session('org_id') == $org->org_id) {
            return redirect()
                ->route('orgs.index')
                ->with('error', 'No puedes eliminar la organización activa.');
        }

        $org->delete();

        return redirect()
            ->route('orgs.index')
            ->with('success', 'Organización eliminada correctamente.');
    }

    /**
     * Activar una organización (helper)
     */
    public function activate(Org $org)
    {
        $this->setActiveOrg($org->org_id);

        return redirect()
            ->route('orgs.index')
            ->with('success', "Organización '{$org->name}' activada correctamente.");
    }
    public function checkRuc(Request $request)
    {
        $exists = \App\Models\Core\Org::where('ruc', $request->ruc)->exists();

        return response()->json(!$exists);
    }


    /**
     * Método privado para actualizar la sesión de organización activa
     */
    private function setActiveOrg($orgId)
    {
        session(['org_id' => $orgId]);
    }
}
