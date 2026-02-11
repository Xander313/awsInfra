<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Core\Org;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class OrgController extends Controller
{
    /**
     * Mostrar todas las organizaciones
     */
    public function index()
    {
        $orgs = Org::orderBy('name')->get();
        $activeOrgId = session('org_id');

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

        // Activar automáticamente la nueva organización
        $this->setActiveOrg($org->org_id);

        return redirect()
            ->route('orgs.index')
            ->with('success', 'Organización creada y activada.');
    }

    /**
     * Mostrar detalles
     */
    public function show(Org $org)
    {
        return view('core.org.show', compact('org'));
    }

    /**
     * Formulario editar
     */
    public function edit(Org $org)
    {
        return view('core.org.edit', compact('org'));
    }

    /**
     * Actualizar organización
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
        // 1️⃣ No permitir eliminar la organización activa
        if (session('org_id') == $org->org_id) {
            return redirect()
                ->route('orgs.index')
                ->with('error', 'No puedes eliminar la organización activa.');
        }

        // 2️⃣ Validar si tiene auditorías relacionadas
        if ($org->audits()->exists()) {
            return redirect()
                ->route('orgs.index')
                ->with('error', 'No se puede eliminar la organización porque tiene auditorías asociadas.');
        }

        try {
            $org->delete();

            return redirect()
                ->route('orgs.index')
                ->with('success', 'Organización eliminada correctamente.');

        } catch (QueryException $e) {

            return redirect()
                ->route('orgs.index')
                ->with('error', 'No se puede eliminar la organización porque existen datos relacionados.');
        }
    }

    /**
     * Activar organización
     */
    public function activate(Org $org)
    {
        $this->setActiveOrg($org->org_id);

        return redirect()
            ->route('orgs.index')
            ->with('success', "Organización '{$org->name}' activada correctamente.");
    }

    /**
     * Validación AJAX de RUC
     */
    public function checkRuc(Request $request)
    {
        $exists = Org::where('ruc', $request->ruc)->exists();

        return response()->json(!$exists);
    }

    /**
     * Helper privado para activar organización en sesión
     */
    private function setActiveOrg($orgId)
    {
        session(['org_id' => $orgId]);
    }
}
