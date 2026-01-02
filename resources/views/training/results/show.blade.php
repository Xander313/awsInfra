@extends('layouts.app')

@section('title', 'Detalle del Resultado')

@section('content')
<div class="flex justify-center mt-10">
    <div class="w-full max-w-xl">

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">

            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    Detalle del Resultado
                </h2>
            </div>

            <div class="p-6 space-y-4 text-sm">
                <p><strong>Usuario:</strong> {{ $result->assignment->user->full_name }}</p>
                <p><strong>Curso:</strong> {{ $result->assignment->course->name }}</p>
                <p><strong>Fecha:</strong> {{ $result->completed_at }}</p>
                <p><strong>Puntaje:</strong> {{ $result->score ?? '—' }}</p>

                <div class="pt-4 border-t">
                    <a href="{{ route('training.results.index') }}"
                       class="text-blue-600 hover:underline">
                        Volver
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
