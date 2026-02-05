@extends('layouts.auth')

@section('title', 'Verificar correo')

@section('content')
<div class="min-h-screen flex bg-white">
    <div class="hidden lg:block relative w-0 flex-1">
        <img class="absolute inset-0 h-full w-full object-cover" src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80" alt="Edificio Corporativo">
        <div class="absolute inset-0 bg-blue-900 bg-opacity-40 mix-blend-multiply"></div>
        <div class="absolute inset-0 flex flex-col justify-center px-12 text-white">
            <h2 class="text-4xl font-extrabold tracking-tight mb-4">Verifica tu correo</h2>
            <p class="text-lg text-blue-100 max-w-md">Ingresa el código de 6 dígitos para activar tu cuenta.</p>
        </div>
    </div>

    <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-gray-50">
        <div class="mx-auto w-full max-w-sm lg:w-96">
            <div class="text-center lg:text-left">
                <div class="flex justify-center lg:justify-start items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-800 rounded-lg shadow-lg flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5s-3 1.343-3 3 1.343 3 3 3zm0 2c-2.67 0-8 1.337-8 4v2h16v-2c0-2.663-5.33-4-8-4z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-gray-900 tracking-tight">SGPD</span>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Verificación de correo</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Enviamos un código a <span class="font-semibold">{{ $email ?? '' }}</span>
                </p>
            </div>

            <div class="mt-8">
                @if (session('success'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-md shadow-sm">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3 text-sm text-green-700">
                                {{ session('success') }}
                            </div>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <ul class="list-disc list-inside text-sm text-red-700">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('verify_email.post') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">Código</label>
                        <input id="code" name="code" maxlength="6" inputmode="numeric" required
                               class="mt-1 block w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
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
    </div>
</div>
@endsection
