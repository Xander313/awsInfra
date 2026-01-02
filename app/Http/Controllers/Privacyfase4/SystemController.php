<?php

namespace App\Http\Controllers\Privacyfase4;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Privacyfase4\System;
use App\Models\Core\Org;
use App\Models\IAM\AppUser;

class SystemController extends Controller
{
    // LISTAR + FILTRADO
    public function index(Request $request)
    {
        $systems = System::with(['organization', 'owner'])
            ->when($request->filled('name'), function ($q) use ($request) {
                $q->where('name', 'ILIKE', '%' . $request->name . '%');
            })
            ->when($request->filled('type'), function ($q) use ($request) {
                $q->where('type', 'ILIKE', '%' . $request->type . '%');
            })
            ->when($request->filled('criticality'), function ($q) use ($request) {
                $q->where('criticality', $request->criticality);
            })
            ->get();

        return view('privacyfase4.systems.index', compact('systems'));
    }

    // FORM CREAR
    public function create()
    {
        $orgs = Org::all();
        $users = AppUser::all();
        return view('privacyfase4.systems.create', compact('orgs', 'users'));
    }

    // GUARDAR
    public function store(Request $request)
    {
        // 🚫 EVITAR DUPLICADOS (nombre + organización)
        $existe = System::where('name', $request->name)
            ->where('org_id', $request->org_id)
            ->exists();

        if ($existe) {
            return redirect()
                ->back()
                ->withInput()
                ->with([
                    'alert' => 'duplicate',
                    'message' => 'Este sistema ya está registrado.'
                ]);
        }

        $request->validate([
            'org_id' => 'required|integer',
            'owner_user_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'hosting' => 'required|string|max:100',
            'criticality' => 'required|string|max:50',
            'description' => 'required|string'
        ]);

        System::create($request->all());

        return redirect()
            ->route('systems.index')
            ->with([
                'alert' => 'created',
                'message' => 'El sistema fue registrado correctamente.'
            ]);
    }

    // EDITAR
    public function edit($id)
    {
        $system = System::findOrFail($id);
        $orgs = Org::all();
        $users = AppUser::all();

        return view('privacyfase4.systems.edit', compact('system', 'orgs', 'users'));
    }

    // ACTUALIZAR
    public function update(Request $request, $id)
    {
        $request->validate([
            'org_id' => 'required|integer',
            'owner_user_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'hosting' => 'required|string|max:100',
            'criticality' => 'required|string|max:50',
            'description' => 'required|string'
        ]);

        // 🚫 EVITAR DUPLICADOS (excluyendo el actual)
        $existe = System::where('name', $request->name)
            ->where('org_id', $request->org_id)
            ->where('system_id', '!=', $id)
            ->exists();

        if ($existe) {
            return redirect()
                ->back()
                ->withInput()
                ->with([
                    'alert' => 'duplicate',
                    'message' => 'Ya existe otro sistema con el mismo nombre en esta organización.'
                ]);
        }

        $system = System::findOrFail($id);
        $system->update($request->all());

        return redirect()
            ->route('systems.index')
            ->with([
                'alert' => 'updated',
                'message' => 'El sistema fue actualizado correctamente.'
            ]);
    }

    // ELIMINAR
    public function destroy($id)
    {
        $system = System::findOrFail($id);

        if ($system->dataStores()->count() > 0) {
            return redirect()
                ->route('systems.index')
                ->with([
                    'alert' => 'error',
                    'message' => 'No se puede eliminar el sistema porque está siendo utilizado.'
                ]);
        }

        $system->delete();

        return redirect()
            ->route('systems.index')
            ->with([
                'alert' => 'deleted',
                'message' => 'El sistema fue eliminado correctamente.'
            ]);
    }
}
