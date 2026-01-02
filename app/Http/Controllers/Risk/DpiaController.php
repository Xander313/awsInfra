<?php

namespace App\Http\Controllers\Risk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Risk\Dpia;
use App\Models\Risk\Risk;
use App\Models\Privacy\ProcessingActivity;

class DpiaController extends Controller
{
    /**
     * Listado de DPIA. Opcional: filtrar por pa_id (?pa_id=)
     */
    public function index(Request $request)
    {
        $query = Dpia::with(['processingActivity']);

        if ($request->filled('pa_id')) {
            $query->where('pa_id', (int) $request->query('pa_id'));
        }

        $dpias = $query->orderBy('dpia_id', 'desc')->get();
        return response()->json($dpias);
    }

    /**
     * Meta: listado de Processing Activities (privacy.processing_activity) para dropdowns.
     * GET /risk/meta/processing-activities?org_id=1&q=texto
     */
    public function processingActivities(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $orgId = $request->query('org_id');

        $query = ProcessingActivity::query()->select('pa_id', 'org_id', 'name');

        if ($orgId !== null && $orgId !== '' && $orgId !== 'all') {
            $query->where('org_id', (int) $orgId);
        }

        if ($q !== '') {
            // PostgreSQL: ilike (case-insensitive)
            $query->where('name', 'ilike', "%{$q}%");
        }

        return response()->json(
            $query->orderBy('pa_id', 'desc')->limit(500)->get()
        );
    }

    /**
     * Crea un DPIA ligado a una ProcessingActivity (pa_id).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'pa_id' => ['required', 'integer'],
            'status' => ['nullable', 'string', 'max:50'],
            'summary' => ['nullable', 'string'],
        ]);

        // Validación manual (evita problema de Validator con schema.table en "exists")
        $paId = (int) $data['pa_id'];
        if (!ProcessingActivity::where('pa_id', $paId)->exists()) {
            return response()->json([
                'message' => 'pa_id no existe en privacy.processing_activity.',
            ], 422);
        }

        $data['status'] = $data['status'] ?? 'draft';

        $dpia = null;
        DB::transaction(function () use (&$dpia, $data) {
            $dpia = Dpia::create([
                'pa_id' => $data['pa_id'],
                'initiated_at' => now(),
                'status' => $data['status'],
                'summary' => $data['summary'] ?? null,
            ]);
        });

        return response()->json($dpia, 201);
    }

    public function show(string $id)
    {
        $dpia = Dpia::with(['processingActivity', 'risks'])->findOrFail($id);
        return response()->json($dpia);
    }

    public function update(Request $request, string $id)
    {
        $dpia = Dpia::findOrFail($id);

        $data = $request->validate([
            'status' => ['sometimes', 'nullable', 'string', 'max:50'],
            'summary' => ['sometimes', 'nullable', 'string'],
        ]);

        $dpia->update($data);
        return response()->json($dpia);
    }

    public function destroy(string $id)
    {
        $dpia = Dpia::findOrFail($id);

        DB::transaction(function () use ($dpia) {
            // Limpia pivote para evitar errores por FK
            $dpia->risks()->detach();
            $dpia->delete();
        });

        return response()->json(['message' => 'DPIA eliminado.']);
    }

    /**
     * Adjuntar un Risk a un DPIA con rationale (upsert).
     * POST /risk/dpias/{dpia}/risks
     */
    public function attachRisk(Request $request, string $dpiaId)
    {
        $dpia = Dpia::findOrFail($dpiaId);

        $data = $request->validate([
            'risk_id' => ['required', 'integer'],
            'rationale' => ['nullable', 'string'],
        ]);

        $riskId = (int) $data['risk_id'];
        if (!Risk::where('risk_id', $riskId)->exists()) {
            return response()->json([
                'message' => 'risk_id no existe en risk.risk.',
            ], 422);
        }

        $dpia->risks()->syncWithoutDetaching([
            $riskId => ['rationale' => $data['rationale'] ?? null],
        ]);

        return response()->json($dpia->load('risks'));
    }

    /**
     * Actualiza solo el rationale de una relación DPIA<->Risk.
     * PUT /risk/dpias/{dpia}/risks/{risk}
     */
    public function updateRiskRationale(Request $request, string $dpiaId, string $riskId)
    {
        $dpia = Dpia::findOrFail($dpiaId);

        $data = $request->validate([
            'rationale' => ['nullable', 'string'],
        ]);

        $dpia->risks()->updateExistingPivot((int) $riskId, [
            'rationale' => $data['rationale'] ?? null,
        ]);

        return response()->json($dpia->load('risks'));
    }

    /**
     * Desasocia un Risk de un DPIA.
     * DELETE /risk/dpias/{dpia}/risks/{risk}
     */
    public function detachRisk(string $dpiaId, string $riskId)
    {
        $dpia = Dpia::findOrFail($dpiaId);
        $dpia->risks()->detach((int) $riskId);

        return response()->json($dpia->load('risks'));
    }

    /**
     * Resumen: DPIA(s) por actividad y riesgos asociados.
     * GET /risk/processing-activities/{pa_id}/dpia-summary
     */
    public function summaryByActivity(string $paId)
    {
        $paId = (int) $paId;

        $activity = ProcessingActivity::find($paId);
        if (!$activity) {
            return response()->json([
                'message' => 'ProcessingActivity no encontrada.',
            ], 404);
        }

        $dpias = Dpia::with('risks')
            ->where('pa_id', $paId)
            ->orderBy('dpia_id', 'desc')
            ->get();

        $riskCount = $dpias->flatMap(function ($d) {
            return $d->risks ? $d->risks->pluck('risk_id') : collect();
        })->unique()->count();

        return response()->json([
            'activity' => $activity,
            'dpias' => $dpias,
            'dpia_count' => $dpias->count(),
            'risk_count' => $riskCount,
        ]);
    }
}
