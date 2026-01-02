<?php

namespace App\Http\Controllers\Privacy;

use App\Http\Controllers\Controller;
use App\Models\Privacy\DataSubject;
use App\Models\Privacy\Consent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataSubjectController extends Controller
{
    public function index(Request $request)
    {
        // Verificar si hay organización activa
        if (!session('org_id')) {
            return redirect()->route('orgs.index')
                ->with('error', 'Debes seleccionar una organización primero.');
        }

        $query = DataSubject::where('org_id', session('org_id'))
            ->with(['consents' => function($q) {
                $q->orderBy('given_at', 'desc');
            }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id_number', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $dataSubjects = $query->orderBy('full_name')->paginate(20);

        return view('consent.index', compact('dataSubjects'));
    }

    public function create()
    {
        if (!session('org_id')) {
            return redirect()->route('orgs.index')
                ->with('error', 'Debes seleccionar una organización primero.');
        }

        return view('consent.create');
    }

    public function store(Request $request)
    {
        if (!session('org_id')) {
            return back()->with('error', 'No hay una organización activa. Por favor, selecciona una organización primero.')->withInput();
        }
        
        $request->validate([
            'id_type' => 'required|string|max:50',
            'id_number' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($request) {
                    if (!session('org_id')) {
                        $fail('No hay organización activa.');
                        return;
                    }
                    
                    $exists = DataSubject::where('org_id', session('org_id'))
                        ->where('id_type', $request->id_type)
                        ->where('id_number', $value)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Este número de identificación ya existe para este tipo en la organización actual.');
                    }
                },
            ],
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'verified_level' => 'nullable|integer|min:0|max:10'
        ]);

        try {
            DataSubject::create([
                'org_id' => session('org_id'),
                'id_type' => $request->id_type,
                'id_number' => $request->id_number,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'verified_level' => $request->verified_level ?? 0
            ]);

            return redirect()
                ->route('data-subjects.index')
                ->with('success', 'Titular registrado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear el titular: ' . $e->getMessage())->withInput();
        }
    }

    public function show(DataSubject $dataSubject)
    {
        if (!session('org_id')) {
            return redirect()->route('orgs.index')
                ->with('error', 'Debes seleccionar una organización primero.');
        }

        if ($dataSubject->org_id != session('org_id')) {
            abort(403, 'No autorizado');
        }

        $dataSubject->load(['org', 'consents']);
        
        $consents = $dataSubject->consents()->orderBy('given_at', 'desc')->get();
        
        return view('consent.show', compact('dataSubject', 'consents'));
    }

    public function edit(DataSubject $dataSubject)
    {
        if (!session('org_id')) {
            return redirect()->route('orgs.index')
                ->with('error', 'Debes seleccionar una organización primero.');
        }

        if ($dataSubject->org_id != session('org_id')) {
            abort(403, 'No autorizado');
        }

        return view('consent.edit', compact('dataSubject'));
    }

    public function update(Request $request, DataSubject $dataSubject)
    {
        if ($dataSubject->org_id != session('org_id')) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'id_type' => 'required|string|max:50',
            'id_number' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($request, $dataSubject) {
                    $exists = DataSubject::where('org_id', session('org_id'))
                        ->where('id_type', $request->id_type)
                        ->where('id_number', $value)
                        ->where('subject_id', '!=', $dataSubject->subject_id)
                        ->exists();
                    
                    if ($exists) {
                        $fail('Este número de identificación ya existe para este tipo en la organización actual.');
                    }
                },
            ],
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'verified_level' => 'nullable|integer|min:0|max:10'
        ]);

        try {
            $dataSubject->update([
                'id_type' => $request->id_type,
                'id_number' => $request->id_number,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'verified_level' => $request->verified_level ?? $dataSubject->verified_level
            ]);

            return redirect()
                ->route('data-subjects.index')
                ->with('success', 'Titular actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(DataSubject $dataSubject)
    {
        if ($dataSubject->org_id != session('org_id')) {
            abort(403, 'No autorizado');
        }

        if ($dataSubject->consents()->count() > 0) {
            return redirect()
                ->route('data-subjects.index')
                ->with('error', 'No se puede eliminar un titular con consentimientos registrados.');
        }

        try {
            $dataSubject->delete();
            return redirect()
                ->route('data-subjects.index')
                ->with('success', 'Titular eliminado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    public function createConsent(DataSubject $dataSubject)
    {
        if ($dataSubject->org_id != session('org_id')) {
            abort(403, 'No autorizado');
        }

        // Verificar si ya tiene consentimiento activo
        if ($dataSubject->activeConsent()) {
            return redirect()
                ->route('data-subjects.show', $dataSubject)
                ->with('error', 'Este titular ya tiene un consentimiento activo.');
        }

        return view('consent.create-consent', compact('dataSubject'));
    }

    public function storeConsent(Request $request, DataSubject $dataSubject)
    {
        if ($dataSubject->org_id != session('org_id')) {
            abort(403, 'No autorizado');
        }

        $request->validate([
            'notice_ver_id' => 'nullable|integer',
            'purpose_id' => 'nullable|integer',
            'given_at' => 'nullable|date'
        ]);

        DB::beginTransaction();
        try {
            // Revocar consentimiento activo anterior si existe
            $dataSubject->consents()->whereNull('revoked_at')->update(['revoked_at' => now()]);
            
            Consent::create([
                'subject_id' => $dataSubject->subject_id,
                'notice_ver_id' => $request->notice_ver_id ?: null,
                'purpose_id' => $request->purpose_id ?: null,
                'given_at' => $request->given_at ?: now(),
                'revoked_at' => null
            ]);

            DB::commit();
            
            return redirect()
                ->route('data-subjects.show', $dataSubject)
                ->with('success', 'Consentimiento registrado correctamente.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar consentimiento: ' . $e->getMessage());
        }
    }

    public function revokeConsent(Request $request, Consent $consent)
    {
        if ($consent->dataSubject->org_id != session('org_id')) {
            abort(403, 'No autorizado');
        }

        if ($consent->revoked_at) {
            return back()->with('error', 'El consentimiento ya fue revocado anteriormente.');
        }

        try {
            $consent->update([
                'revoked_at' => now()
            ]);

            return redirect()
                ->route('data-subjects.show', $consent->dataSubject)
                ->with('success', 'Consentimiento revocado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al revocar consentimiento: ' . $e->getMessage());
        }
    }
}