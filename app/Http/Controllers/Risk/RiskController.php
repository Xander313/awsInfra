<?php

namespace App\Http\Controllers\Risk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Risk\Risk;
use App\Models\Risk\Org;

class RiskController extends Controller
{
    public function index(Request $request)
    {
        // En RAT.zip se hardcodea org_id = 1 para inserts (temporal).
        $orgId = $request->query('org_id');

        $query = Risk::query();
        if ($orgId !== null && $orgId !== '' && $orgId !== 'all') {
            $query->where('org_id', (int) $orgId);
        }

        $risks = $query->orderBy('risk_id', 'desc')->get();
        return response()->json($risks);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'org_id' => ['nullable', 'integer'],
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'risk_type' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        if (!isset($data['org_id'])) {
            $data['org_id'] = 1;
        }

        $risk = Risk::create($data);
        return response()->json($risk, 201);
    }

    

/**
 * Meta: listado de organizaciones (core.org) para poblar dropdowns en UI.
 */
public function orgs(Request $request)
{
    $q = trim((string) $request->query('q', ''));

    $query = Org::query()->select('org_id', 'name', 'ruc', 'industry');
    if ($q !== '') {
        $query->where(function ($w) use ($q) {
            $w->where('name', 'ilike', "%{$q}%")
              ->orWhere('ruc', 'ilike', "%{$q}%");
        });
    }

    return response()->json($query->orderBy('name')->limit(200)->get());
}

public function show(string $id)
    {
        $risk = Risk::findOrFail($id);
        return response()->json($risk);
    }

    public function update(Request $request, string $id)
    {
        $risk = Risk::findOrFail($id);

        $data = $request->validate([
            'org_id' => ['sometimes', 'nullable', 'integer'],
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'risk_type' => ['sometimes', 'nullable', 'string', 'max:50'],
            'status' => ['sometimes', 'nullable', 'string', 'max:50'],
        ]);

        $risk->update($data);
        return response()->json($risk);
    }

    public function destroy(string $id)
    {
        $risk = Risk::findOrFail($id);

        // Evita error por FK si el riesgo ya está asociado a algún DPIA
        if ($risk->dpias()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el riesgo porque está asociado a uno o más DPIA (dpia_risk).',
            ], 409);
        }

        $risk->delete();
        return response()->json(['message' => 'Riesgo eliminado.']);
    }
}
