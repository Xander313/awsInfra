@extends('layouts.auth')

@section('title', 'Sesión ya abierta')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full px-6">
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-amber-900">
            <h2 class="text-base font-semibold">Sesión activa en otro dispositivo</h2>
            <p class="mt-1 text-sm">Tu cuenta ya estaba abierta. Elige qué hacer.</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'warning',
            title: 'Sesión ya abierta',
            text: 'Tu cuenta ya estaba abierta en otro dispositivo o navegador.',
            confirmButtonText: 'Mantener sesión aquí',
            cancelButtonText: 'Volver a inicio',
            showCancelButton: true,
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showCloseButton: false,
            showLoaderOnConfirm: true,
            preConfirm: async () => {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const response = await fetch('{{ route('session.takeover') }}', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    let data = {};
                    let text = '';
                    try {
                        data = await response.clone().json();
                    } catch (_) {
                        try {
                            text = await response.text();
                        } catch (_) {
                            // no-op
                        }
                    }

                    if (!response.ok) {
                        const detail = data && data.message ? data.message : (text ? text.slice(0, 160) : '');
                        const status = response.status ? `(${response.status}) ` : '';
                        throw new Error(status + (detail || 'takeover-failed'));
                    }

                    return data || {};
                } catch (error) {
                    const msg = error && error.message ? error.message : 'No se pudo mantener la sesión aquí. Intenta de nuevo.';
                    Swal.showValidationMessage(`No se pudo mantener la sesión aquí. ${msg}`);
                    return false;
                }
            }
        }).then(async (result) => {
            if (result.isConfirmed) {
                const redirect = result.value && result.value.redirect ? result.value.redirect : '{{ route('dashboard') }}';
                window.location.href = redirect;
                return;
            }

            if (result.dismiss === Swal.DismissReason.cancel) {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    await fetch('{{ route('logout') }}', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                } catch (_) {
                    // no-op
                }
                window.location.href = '{{ route('login') }}';
            }
        });
    });
</script>
@endpush
@endsection
