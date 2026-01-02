<?php

namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Models\Document\Document;
use App\Models\Document\DocumentVersion;
use App\Models\Core\Org;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Listado de documentos
     */
    public function index()
    {
        $query = Document::query();

        $documents = $query
            ->with(['org', 'creator', 'activeVersion'])
            ->orderBy('doc_id', 'asc')
            ->paginate(15);

        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        $orgs = Org::orderBy('name')->get();

        return view('documents.create', compact('orgs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'org_id' => [
                'required',
                'integer',
                Rule::exists(Org::class, 'org_id'),
            ],
            'title'          => 'required|string|max:255',
            'doc_type'       => 'nullable|string|max:100',
            'classification' => 'nullable|string|max:50',
            'file'           => 'required|file|max:10240',
        ]);

        $user = Auth::user();
        $createdBy = $user?->user_id ?? null;

        $document = Document::create([
            'org_id'        => $request->org_id,
            'title'         => $request->title,
            'doc_type'      => $request->doc_type,
            'classification'=> $request->classification,
            'created_by'    => $createdBy,
        ]);

        $this->createNewVersion($document, $request->file('file'));

        return redirect()
            ->route('documents.index')
            ->with('success', 'Documento creado correctamente.');
    }

    public function show(Document $document)
    {
        $document->load(['org', 'creator', 'versions']);

        return view('documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        return view('documents.edit', compact('document'));
    }

    public function update(Request $request, Document $document)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'doc_type'      => 'nullable|string|max:100',
            'classification'=> 'nullable|string|max:50',
        ]);

        $document->update($request->only([
            'title',
            'doc_type',
            'classification',
        ]));

        return redirect()
            ->route('documents.show', $document->doc_id)
            ->with('success', 'Documento actualizado correctamente.');
    }

    public function destroy(Document $document)
    {
        $document->versions()->delete();
        $document->delete();

        return redirect()
            ->route('documents.index')
            ->with('success', 'Documento eliminado correctamente.');
    }

    public function createVersion(Document $document)
    {
        return view('documents.upload_version', compact('document'));
    }

    public function storeVersion(Request $request, Document $document)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $this->createNewVersion($document, $request->file('file'));

        return redirect()
            ->route('documents.show', $document->doc_id)
            ->with('success', 'Nueva versión subida correctamente.');
    }

    public function activateVersion(Document $document, DocumentVersion $version)
    {
        DocumentVersion::where('doc_id', $document->doc_id)
            ->update(['active_flag' => false]);

        $version->update(['active_flag' => true]);

        return redirect()
            ->route('documents.show', $document->doc_id)
            ->with('success', 'Versión marcada como activa.');
    }

    public function downloadVersion(Document $document, DocumentVersion $version)
    {
        if (!Storage::disk('public')->exists($version->file_uri)) {
            abort(404, 'Archivo no encontrado.');
        }

        return Storage::disk('public')->download(
            $version->file_uri,
            $document->title . '_v' . $version->version_no
        );
    }

    protected function createNewVersion(Document $document, $file)
    {
        $path = $file->store('documents', 'public');
        $checksum = hash_file('sha256', Storage::disk('public')->path($path));

        $nextVersion = (DocumentVersion::where('doc_id', $document->doc_id)->max('version_no') ?? 0) + 1;

        DocumentVersion::where('doc_id', $document->doc_id)
            ->update(['active_flag' => false]);

        DocumentVersion::create([
            'doc_id'      => $document->doc_id,
            'version_no'  => $nextVersion,
            'file_uri'    => $path,
            'checksum'    => $checksum,
            'active_flag' => true,
        ]);
    }
}
