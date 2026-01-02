<div class="border rounded p-4 bg-gray-50">
    <h3 class="font-semibold mb-2">Evidencias</h3>

    <p class="text-sm text-gray-600 mb-3">
        Aquí se adjuntarán los documentos que respaldan la solicitud DSAR.
    </p>

    {{-- LISTA DE EVIDENCIAS --}}
    @if($dsar->evidences && $dsar->evidences->count())
        <ul class="space-y-2">
            @foreach($dsar->evidences as $ev)
                <li class="border p-2 rounded bg-white">
                    <p class="text-sm font-medium">{{ $ev->description }}</p>
                    <p class="text-xs text-gray-500">
                        Adjuntado: {{ $ev->attached_at }}
                    </p>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-sm text-gray-500">
            No existen evidencias registradas.
        </p>
    @endif
</div>
