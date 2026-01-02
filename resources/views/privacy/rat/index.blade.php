@extends('layouts.app')

@section('title', 'RAT')
@section('active_key', 'rat')

@section('page_header')
<div class="flex justify-between items-center mb-4">
    <div>
        <h2 class="text-xl font-bold">Actividades de Tratamiento</h2>
        <p class="text-sm text-gray-500">Registro RAT</p>
    </div>

    <a href="{{ route('rat.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center gap-2">
        <i class="fa-solid fa-plus"></i>
        Nuevo
    </a>
</div>
@endsection

@section('content')


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

@if(session('exito'))
<script>
document.addEventListener('DOMContentLoaded', function () {

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });

    Toast.fire({
        icon: 'success',
        title: "{{ session('exito') }}"
    });
});
</script>
@endif

{{-- ================= TOAST ERROR ================= --}}
@if(session('erro'))
<script>
document.addEventListener('DOMContentLoaded', function () {

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
    });

    Toast.fire({
        icon: 'error',
        title: "{{ session('erro') }}"
    });
});
</script>
@endif

{{-- ================= LISTADO ================= --}}
<div class="bg-white border rounded divide-y">

    @forelse($activities as $a)
        <div class="p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">

            {{-- INFO --}}
            <div>
                <h3 class="font-semibold text-gray-800">
                    {{ $a->name }}
                </h3>
            </div>

            {{-- ACCIONES --}}
            <div class="flex gap-2">

                <a href="{{ route('rat.edit', $a->pa_id) }}"
                   title="Editar"
                   class="w-9 h-9 flex items-center justify-center rounded bg-yellow-500 hover:bg-yellow-600 text-white">
                    <i class="fa-solid fa-pen"></i>
                </a>

                <button type="button"
                        title="Eliminar"
                        onclick="confirmDelete({{ $a->pa_id }})"
                        class="w-9 h-9 flex items-center justify-center rounded bg-red-600 hover:bg-red-700 text-white">
                    <i class="fa-solid fa-trash"></i>
                </button>

                <form id="delete-form-{{ $a->pa_id }}"
                      action="{{ route('rat.destroy', $a->pa_id) }}"
                      method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>

        </div>
    @empty
        <p class="p-4 text-gray-500 text-center">
            No hay actividades registradas
        </p>
    @endforelse

</div>

<script>
function confirmDelete(id) {

    Swal.fire({
        title: '¿Eliminar actividad?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>

@endsection
