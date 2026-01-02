<?php

namespace App\Http\Controllers\Privacyfase4;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Privacyfase4\DataStore;
use App\Models\Privacyfase4\System;

class DataStoreController extends Controller
{
    // LISTAR
    public function index(Request $request)
    {
        $systems = System::all();

        $dataStores = DataStore::query()
            ->when($request->filled('name'), function ($q) use ($request) {
                $q->whereHas('system', function ($q2) use ($request) {
                    $q2->where('name', 'ILIKE', '%' . $request->name . '%');
                });
            })
            ->when($request->filled('system_id'), function ($q) use ($request) {
                $q->where('system_id', $request->system_id);
            })
            ->get();

        return view('privacyfase4.data-stores.index', compact('dataStores', 'systems'));
    }

    // LISTAR DataStores por sistema (SUBRECURSO)
    public function indexBySystem(System $system)
    {
        $dataStores = $system->dataStores; // Relación definida en System
        $systems = System::all(); // Para posibles filtros

        return view('privacyfase4.data-stores.index', compact('system', 'dataStores', 'systems'));
    }

    // FORM CREAR
    public function create()
    {
        $systems = System::all();
        return view('privacyfase4.data-stores.create', compact('systems'));
    }

    // GUARDAR (EVITAR DUPLICADOS)
    public function store(Request $request)
    {
        $request->validate([
            'system_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'store_type' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'encryption_flag' => 'nullable|boolean',
            'backup_flag' => 'nullable|boolean'
        ]);

        // 🚫 EVITAR DUPLICADOS (mismo sistema + mismo nombre, sin importar mayúsculas)
        $existe = DataStore::where('system_id', $request->system_id)
            ->where('name', 'ILIKE', trim($request->name))
            ->exists();

        if ($existe) {
            return redirect()
                ->back()
                ->withInput()
                ->with([
                    'alert' => 'duplicate',
                    'message' => 'Este almacén de datos ya está registrado.'
                ]);
        }

        DataStore::create($request->all());

        return redirect()
            ->route('data-stores.index')
            ->with([
                'alert' => 'created',
                'message' => 'El almacén de datos fue registrado correctamente.'
            ]);
    }

    // EDITAR
    public function edit($id)
    {
        $dataStore = DataStore::findOrFail($id);
        $systems = System::all();

        return view('privacyfase4.data-stores.edit', compact('dataStore', 'systems'));
    }

    // ACTUALIZAR (EVITAR DUPLICADOS)
    public function update(Request $request, $id)
    {
        $request->validate([
            'system_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'store_type' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'encryption_flag' => 'nullable|boolean',
            'backup_flag' => 'nullable|boolean'
        ]);

        // 🚫 EVITAR DUPLICADOS (excluyendo el registro actual)
        $existe = DataStore::where('system_id', $request->system_id)
            ->where('name', 'ILIKE', trim($request->name))
            ->where('store_id', '!=', $id)
            ->exists();

        if ($existe) {
            return redirect()
                ->back()
                ->withInput()
                ->with([
                    'alert' => 'duplicate',
                    'message' => 'Este almacén de datos ya está registrado.'
                ]);
        }

        $dataStore = DataStore::findOrFail($id);
        $dataStore->update($request->all());

        return redirect()
            ->route('data-stores.index')
            ->with([
                'alert' => 'updated',
                'message' => 'El almacén de datos fue actualizado correctamente.'
            ]);
    }

    // ELIMINAR
    public function destroy($id)
    {
        $dataStore = DataStore::findOrFail($id);
        $dataStore->delete();

        return redirect()
            ->route('data-stores.index')
            ->with([
                'alert' => 'deleted',
                'message' => 'El almacén de datos fue eliminado correctamente.'
            ]);
    }
}

