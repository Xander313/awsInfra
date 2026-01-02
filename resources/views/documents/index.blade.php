@extends('layouts.app')

@section('content')
<div class="container mt-5">

    {{-- Título + botón nuevo --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-file-earmark-text"></i> Documentos
        </h2>

        <a href="{{ route('documents.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Documento
        </a>
    </div>

    {{-- Card con tabla, igual estilo que organizaciones --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">

            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Clasificación</th>
                        <th>Organización</th>
                        <th>Creador</th>
                        <th class="text-center">Versión activa</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($documents as $doc)
                        <tr>
                            <td class="fw-semibold">
                                {{ $doc->doc_id }}
                            </td>

                            <td>
                                {{ $doc->title }}
                            </td>

                            <td>
                                {{ $doc->doc_type ?? '—' }}
                            </td>

                            <td>
                                {{ $doc->classification ?? '—' }}
                            </td>

                            <td>
                                {{ $doc->org?->name ?? 'N/A' }}
                            </td>

                            <td>
                                {{ $doc->creator?->full_name ?? 'N/A' }}
                            </td>

                            <td class="text-center">
                                @if($doc->activeVersion)
                                    <span class="badge bg-success">
                                        v{{ $doc->activeVersion->version_no }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        Sin versión
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">

                                    {{-- Ver --}}
                                    <a href="{{ route('documents.show', $doc->doc_id) }}"
                                    class="btn btn-light btn-sm border rounded-pill px-3 shadow-sm"
                                    title="Ver">
                                        <i class="bi bi-eye text-primary"></i> 
                                    </a>

                                    {{-- Editar --}}
                                    <a href="{{ route('documents.edit', $doc->doc_id) }}"
                                    class="btn btn-light btn-sm border rounded-pill px-3 shadow-sm"
                                    title="Editar">
                                        <i class="bi bi-pencil text-warning"></i> 
                                    </a>

                                    {{-- Eliminar --}}
                                    <form action="{{ route('documents.destroy', $doc->doc_id) }}"
                                        method="POST"
                                        onsubmit="return confirm('¿Seguro que deseas eliminar este documento?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-light btn-sm border rounded-pill px-3 shadow-sm"
                                                title="Eliminar">
                                            <i class="bi bi-trash text-danger"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                No hay documentos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

    {{-- Paginación --}}
    <div class="mt-3">
        {{ $documents->links() }}
    </div>
</div>
@endsection
