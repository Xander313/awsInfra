@extends('layouts.auth')

@section('title', 'Verificar correo')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-gray-50 p-6">
  <div class="w-full max-w-md bg-white shadow-xl rounded-xl border border-gray-200 p-6">
    <h2 class="text-2xl font-bold text-gray-900 text-center">Verificación de correo</h2>
    <p class="text-sm text-gray-600 text-center mt-2">
      Enviamos un código a <b>{{ $email ?? '' }}</b>
    </p>

    @if (session('success'))
      <div class="mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
        {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        <ul class="list-disc list-inside">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('verify_email.post') }}" class="mt-6 space-y-4">
      @csrf
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Código</label>
        <input name="code" maxlength="6" inputmode="numeric" required
               class="block w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
               placeholder="123456" value="{{ old('code') }}">
      </div>

      <button type="submit"
              class="w-full py-3 px-4 rounded-lg text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800">
        Verificar y crear cuenta
      </button>
    </form>

    <form method="POST" action="{{ route('verify_email.resend') }}" class="mt-4">
      @csrf
      <button type="submit" class="w-full py-2 px-4 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
        Reenviar código
      </button>
    </form>
  </div>
</div>
@endsection
