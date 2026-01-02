@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 pt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil-square"></i> Editar Titular
                        </h4>
                        <a href="{{ route('data-subjects.show', $dataSubject) }}" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('data-subjects.update', $dataSubject) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- Tipo de Identificación -->
                            <div class="col-md-4">
                                <label for="id_type" class="form-label">Tipo de ID *</label>
                                <select name="id_type" id="id_type" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="Cédula" {{ $dataSubject->id_type == 'Cédula' ? 'selected' : '' }}>Cédula</option>
                                    <option value="Pasaporte" {{ $dataSubject->id_type == 'Pasaporte' ? 'selected' : '' }}>Pasaporte</option>
                                    <option value="RUC" {{ $dataSubject->id_type == 'RUC' ? 'selected' : '' }}>RUC</option>
                                    <option value="Otro" {{ $dataSubject->id_type == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('id_type')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Número de Identificación -->
                            <div class="col-md-8">
                                <label for="id_number" class="form-label">Número de ID *</label>
                                <input type="text" 
                                       name="id_number" 
                                       id="id_number"
                                       class="form-control"
                                       value="{{ old('id_number', $dataSubject->id_number) }}"
                                       required>
                                @error('id_number')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nombre Completo -->
                            <div class="col-12">
                                <label for="full_name" class="form-label">Nombre Completo *</label>
                                <input type="text" 
                                       name="full_name" 
                                       id="full_name"
                                       class="form-control"
                                       value="{{ old('full_name', $dataSubject->full_name) }}"
                                       required>
                                @error('full_name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" 
                                       name="email" 
                                       id="email"
                                       class="form-control"
                                       value="{{ old('email', $dataSubject->email) }}">
                                @error('email')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Teléfono</label>
                                <input type="text" 
                                       name="phone" 
                                       id="phone"
                                       class="form-control"
                                       value="{{ old('phone', $dataSubject->phone) }}">
                                @error('phone')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Dirección -->
                            <div class="col-12">
                                <label for="address" class="form-label">Dirección</label>
                                <textarea name="address" 
                                          id="address" 
                                          class="form-control" 
                                          rows="2">{{ old('address', $dataSubject->address) }}</textarea>
                                @error('address')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Nivel de Verificación -->
                            <div class="col-md-6">
                                <label for="verified_level" class="form-label">Nivel de Verificación</label>
                                <select name="verified_level" id="verified_level" class="form-select">
                                    <option value="0" {{ $dataSubject->verified_level == 0 ? 'selected' : '' }}>Sin verificar</option>
                                    <option value="1" {{ $dataSubject->verified_level == 1 ? 'selected' : '' }}>Nivel 1 - Básico</option>
                                    <option value="2" {{ $dataSubject->verified_level == 2 ? 'selected' : '' }}>Nivel 2 - Medio</option>
                                    <option value="3" {{ $dataSubject->verified_level == 3 ? 'selected' : '' }}>Nivel 3 - Avanzado</option>
                                </select>
                                @error('verified_level')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('data-subjects.show', $dataSubject) }}" 
                                   class="btn btn-outline-secondary">
                                    Cancelar
                                </a>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Guardar Cambios
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection