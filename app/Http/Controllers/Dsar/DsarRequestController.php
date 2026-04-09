<?php

namespace App\Http\Controllers\Dsar;

use App\Http\Controllers\Controller;
use App\Support\DashboardCache;
use Illuminate\Http\Request;

use App\Models\Privacy\DsarRequest;
use App\Models\Privacy\DataSubject;
use App\Models\IAM\AppUser;
use App\Models\Document\DocumentVersion;

class DsarRequestController extends Controller
{
    
    public function index()
    {
        $dsars = DsarRequest::with([
            'subject',
            'assignedUser',
            'evidences.documentVersion.document'
        ])
        ->orderBy('received_at', 'desc')
        ->get();

        return view('privacy.dsar.index', compact('dsars'));
    }

    
    public function create()
    {
        $subjects  = DataSubject::orderBy('full_name')->get();
        $users     = AppUser::orderBy('full_name')->get();

        
        $documents = DocumentVersion::with('document')
            ->where('active_flag', true)
            ->orderBy('doc_ver_id')
            ->get();

        return view(
            'privacy.dsar.create',
            compact('subjects', 'users', 'documents')
        );
    }

    //Guardar
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

        $orgId = (int) (session('org_id') ?? 1);

        DsarRequest::create([
            'org_id' => $orgId,
            'subject_id' => $request->subject_id,
            'request_type' => $request->request_type,
            'channel' => $request->channel,
            'received_at' => $request->received_at,
            'due_at' => $request->due_at,
            'status' => 'PENDING',
            'assigned_to_user_id' => $request->assigned_to_user_id,
        ]);

        DashboardCache::forgetForOrg($orgId);

        return redirect()
            ->route('dsar.index')
            ->with('exito', 'Solicitud DSAR creada correctamente');
    }

    
    public function edit(DsarRequest $dsar)
    {
        
        $dsar->load([
            'evidences.documentVersion.document'
        ]);

        $subjects  = DataSubject::orderBy('full_name')->get();
        $users     = AppUser::orderBy('full_name')->get();

        $documents = DocumentVersion::with('document')
            ->where('active_flag', true)
            ->orderBy('doc_ver_id')
            ->get();

        return view(
            'privacy.dsar.edit',
            compact('dsar', 'subjects', 'users', 'documents')
        );
    }

    
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

        DashboardCache::forgetForOrg((int) $dsar->org_id);

        return redirect()
            ->route('dsar.index')
            ->with('exito', 'Solicitud DSAR actualizada');
    }
}
