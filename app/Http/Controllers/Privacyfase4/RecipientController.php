<?php

namespace App\Http\Controllers\Privacyfase4;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Privacyfase4\Recipient;
use App\Models\Core\Org;

class RecipientController extends Controller
{
    // LISTAR + FILTROS
    public function index(Request $request)
    {
        $orgs = Org::all();

        $recipients = Recipient::with('org')
            // 🔍 buscar por nombre
            ->when($request->filled('name'), function ($q) use ($request) {
                $q->where('name', 'ILIKE', '%' . $request->name . '%');
            })
            // 🔽 filtrar por organización
            ->when($request->filled('org_id'), function ($q) use ($request) {
                $q->where('org_id', $request->org_id);
            })
            ->get();

        return view('privacyfase4.recipients.index', compact('recipients', 'orgs'));
    }

    // FORM CREAR
    public function create()
    {
        $orgs = Org::all();
        return view('privacyfase4.recipients.create', compact('orgs'));
    }

    // GUARDAR
    public function store(Request $request)
    {
        $request->validate([
            'org_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'recipient_type' => 'required|string|max:100',
            'contact_email' => 'nullable|email|max:255',
            'is_third_party' => 'required|in:0,1'
        ]);

        // 🚫 EVITAR DUPLICADOS (nombre + organización)
        $existe = Recipient::where('org_id', $request->org_id)
            ->where('name', $request->name)
            ->exists();

        if ($existe) {
            return redirect()
                ->back()
                ->withInput()
                ->with([
                    'alert' => 'duplicate',
                    'message' => 'Este destinatario ya está registrado para esta organización.'
                ]);
        }

        Recipient::create($request->all());
        
        return redirect()
            ->route('recipients.index')
            ->with([
                'alert' => 'created',
                'message' => 'El destinatario fue registrado correctamente.'
            ]);
    }

    // EDITAR
    public function edit($id)
    {
        $recipient = Recipient::findOrFail($id);
        $orgs = Org::all();

        return view('privacyfase4.recipients.edit', compact('recipient', 'orgs'));
    }

    // ACTUALIZAR
    public function update(Request $request, $id)
    {
        $request->validate([
            'org_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'recipient_type' => 'required|string|max:100',
            'contact_email' => 'nullable|email|max:255',
            'is_third_party' => 'required|in:0,1'
        ]);

        // 🚫 EVITAR DUPLICADOS (excluyendo el actual)
        $existe = Recipient::where('org_id', $request->org_id)
            ->where('name', $request->name)
            ->where('recipient_id', '!=', $id)
            ->exists();

        if ($existe) {
            return redirect()
                ->back()
                ->withInput()
                ->with([
                    'alert' => 'duplicate',
                    'message' => 'Ya existe otro destinatario con ese nombre en esta organización.'
                ]);
        }

        $recipient = Recipient::findOrFail($id);
        $recipient->update($request->all());

        return redirect()
            ->route('recipients.index')
            ->with([
                'alert' => 'updated',
                'message' => 'El destinatario fue actualizado correctamente.'
            ]);
    }

    // ELIMINAR
    public function destroy($id)
    {
        $recipient = Recipient::findOrFail($id);
        $recipient->delete();

        return redirect()
            ->route('recipients.index')
            ->with([
                'alert' => 'deleted',
                'message' => 'El destinatario fue eliminado correctamente.'
            ]);
    }
}
