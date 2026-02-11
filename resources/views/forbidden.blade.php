@extends('layouts.app')

@section('title', 'Acceso en otra pestaña')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-amber-900">
        <h2 class="text-base font-semibold">Sesión activa en otra pestaña</h2>
        <p class="mt-1 text-sm">La aplicación ya está siendo usada por otro usuario.</p>
        <p class="mt-1 text-xs text-amber-800">
            Para continuar aquí, usa el botón del modal para tomar el control en esta pestaña.
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'warning',
            title: 'Sesión activa en otra pestaña',
            text: 'La aplicación ya está siendo utilizada en otra pestaña.',
            confirmButtonText: 'Utilizar el aplicativo en esta pestaña',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showCloseButton: false,
            showCancelButton: false,
            showLoaderOnConfirm: true,
            preConfirm: async () => {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const response = await fetch('/tab/force-claim', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        }
                    });

                    if (!response.ok) {
                        throw new Error('force-claim-failed');
                    }

                    try {
                        const tabId = sessionStorage.getItem('tab_id');
                        if (tabId) {
                            localStorage.setItem('sgpd_active_tab', JSON.stringify({ tabId, ts: Date.now() }));
                        }
                    } catch (_) {
                        // no-op
                    }

                    return true;
                } catch (error) {
                    Swal.showValidationMessage('No se pudo tomar el control. Intenta de nuevo.');
                    return false;
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '/panel';
            }
        });
    });
</script>
@endpush
