@php
    $totalSteps = count($steps);
    $progressPercent = $totalSteps > 0 ? (int) round(($currentStep / $totalSteps) * 100) : 0;
@endphp

<div class="bg-white border rounded p-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <div class="text-muted small">Progreso del asistente</div>
            <div class="fw-semibold">Paso {{ $currentStep }} de {{ $totalSteps }}</div>
        </div>
        <span class="badge text-bg-light border">{{ $progressPercent }}% completado</span>
    </div>

    <div class="progress mb-4" role="progressbar" aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100" style="height: 0.5rem;">
        <div class="progress-bar" style="width: {{ $progressPercent }}%;"></div>
    </div>

    <div class="flex flex-wrap gap-3 items-stretch">
        @foreach ($steps as $number => $step)
            @php
                $isCurrent = $currentStep === $number;
                $isCompleted = in_array($number, $completedSteps, true);
            @endphp
            <div class="flex-1 min-w-[10rem] border rounded px-3 py-3 {{ $isCurrent ? 'border-blue-500 bg-blue-50' : ($isCompleted ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-white') }}">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center rounded-full text-xs fw-bold {{ $isCurrent ? 'bg-blue-600 text-white' : ($isCompleted ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700') }}" style="width: 1.75rem; height: 1.75rem;">
                        {{ $number }}
                    </span>
                    <div>
                        <div class="text-xs uppercase text-gray-500">{{ $step['short'] }}</div>
                        <div class="text-sm fw-semibold text-gray-900">{{ $step['title'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
