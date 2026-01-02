@extends('layouts.app')

@section('title', 'Resultados de Capacitación')

@section('content')
<div class="flex justify-center mt-10">
    <div class="w-full max-w-6xl">

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

            {{-- Header --}}
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">
                    Resultados de Capacitación
                </h2>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left">Usuario</th>
                            <th class="px-6 py-3 text-left">Curso</th>
                            <th class="px-6 py-3 text-center">Fecha</th>
                            <th class="px-6 py-3 text-center">Puntaje</th>
                            <th class="px-6 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($results as $result)
                            <tr>
                                <td class="px-6 py-3">
                                    {{ $result->assignment->user->full_name }}
                                </td>
                                <td class="px-6 py-3">
                                    {{ $result->assignment->course->name }}
                                </td>
                                <td class="px-6 py-3 text-center">
                                    {{ $result->completed_at }}
                                </td>
                                <td class="px-6 py-3 text-center">
                                    {{ $result->score ?? '—' }}
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <a href="{{ route('training.results.show', $result) }}"
                                       class="text-blue-600 hover:underline">
                                        Ver
                                    </a>
                                    |
                                    <a href="{{ route('training.results.edit', $result) }}"
                                       class="text-yellow-600 hover:underline">
                                        Editar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="px-6 py-6 text-center text-gray-500">
                                    No existen resultados registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>
@endsection
