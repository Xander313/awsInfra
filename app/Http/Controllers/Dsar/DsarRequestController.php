<?php

namespace App\Http\Controllers\Dsar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Privacy\DsarRequest;
use App\Models\Privacy\DataSubject;
use App\Models\IAM\AppUser;
use App\Models\Privacy\DocumentVersion;

class DsarRequestController extends Controller
{
    // Listado de DSARs
    public function index()
    {
        $dsars = DsarRequest::with(['subject', 'assignedUser', 'evidences'])
            ->orderBy('received_at', 'desc')
            ->get();

        return view('privacy.dsar.index', compact('dsars'));
    }

    // Crear nuevo DSAR
    public function create()
    {
        $subjects = DataSubject::orderBy('full_name')->get();
        $users    = AppUser::orderBy('full_name')->get();
        $documents = DocumentVersion::orderBy('doc_ver_id')->get();

        return view('privacy.dsar.create', compact('subjects', 'users', 'documents'));
    }

    // Guardar nuevo DSAR
    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:' . DataSubject::class . ',subject_id',
            'request_type' => 'required|string|max:50',
            'channel' => 'required|string|max:50',
            'received_at' => 'required|date',
            'due_at' => 'required|date|after_or_equal:received_at',
            'assigned_to_user_id' => 'nullable|exists:' . AppUser::class . ',user_id',
        ]);

        $dsar = DsarRequest::create([
            'org_id' => 1, // luego lo haces dinámico
            'subject_id' => $request->subject_id,
            'request_type' => $request->request_type,
            'channel' => $request->channel,
            'received_at' => $request->received_at,
            'due_at' => $request->due_at,
            'status' => 'PENDING',
            'assigned_to_user_id' => $request->assigned_to_user_id,
        ]);

        return redirect()
            ->route('dsar.index')
            ->with('exito', 'Solicitud DSAR creada correctamente');
    }

    // Editar DSAR
    public function edit(DsarRequest $dsar)
    {
        $subjects = DataSubject::orderBy('full_name')->get();
        $users    = AppUser::orderBy('full_name')->get();
        $documents = DocumentVersion::orderBy('doc_ver_id')->get(); // importante para evidencias

        return view('privacy.dsar.edit', compact('dsar', 'subjects', 'users', 'documents'));
    }

    // Actualizar DSAR
    public function update(Request $request, DsarRequest $dsar)
    {
        $request->validate([
            'request_type' => 'required|string|max:50',
            'channel' => 'required|string|max:50',
            'due_at' => 'required|date',
            'status' => 'required|in:PENDING,IN_PROGRESS,CLOSED',
            'assigned_to_user_id' => 'nullable|exists:' . AppUser::class . ',user_id',
            'resolution_summary' => 'nullable|string',
        ]);

        $dsar->update([
            'request_type' => $request->request_type,
            'channel' => $request->channel,
            'due_at' => $request->due_at,
            'status' => $request->status,
            'assigned_to_user_id' => $request->assigned_to_user_id,
            'resolution_summary' => $request->resolution_summary,
            'closed_at' => $request->status === 'CLOSED' ? now() : null,
        ]);

        return redirect()
            ->route('dsar.index')
            ->with('exito', 'Solicitud DSAR actualizada');
    }
}