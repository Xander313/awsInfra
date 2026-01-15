<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SGPD COAC')</title>

    {{-- Librerías --}}
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Bootstrap CSS + Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Tipografía global --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Tailwind por CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        :root {
            --sgpd-font: "Montserrat", ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Arial;
        }

        body {
            font-family: var(--sgpd-font);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background: linear-gradient(135deg, #f6f7f9 0%, #eef1f5 100%);
        }
    </style>
</head>

<body class="font-sans">
    @yield('content')

    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                // Cambiar a icono de ojo cerrado (mostrando contraseña)
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
                eyeIcon.classList.add('text-blue-600');
                eyeIcon.classList.remove('text-gray-400');
            } else {
                passwordInput.type = 'password';
                // Cambiar a icono de ojo tachado (ocultando contraseña)
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
                eyeIcon.classList.remove('text-blue-600');
                eyeIcon.classList.add('text-gray-400');
            }
        }
    </script>
    <script>
        // Validación de formularios
        $(document).ready(function() {
            // Validación del login
            $('form[action*="login"]').validate({
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true,
                        minlength: 1
                    }
                },
                messages: {
                    email: {
                        required: "El correo electrónico es requerido",
                        email: "Por favor ingresa un correo válido"
                    },
                    password: {
                        required: "La contraseña es requerida"
                    },
                },
                errorClass: "text-red-500 text-xs italic",
                errorElement: "div",
                highlight: function(element) {
                    $(element).addClass('border-red-500').removeClass('border-gray-300');
                },
                unhighlight: function(element) {
                    $(element).removeClass('border-red-500').addClass('border-gray-300');
                }
            });

            // Validación del registro
            $('form[action*="register"]').validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 2
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true,
                        minlength: 8
                    },
                    password_confirmation: {
                        required: true,
                        equalTo: "#password"
                    },
                },
                messages: {
                    name: {
                        required: "El nombre es requerido",
                        minlength: "El nombre debe tener al menos 2 caracteres"
                    },
                    email: {
                        required: "El correo electrónico es requerido",
                        email: "Por favor ingresa un correo válido"
                    },
                    password: {
                        required: "La contraseña es requerida",
                        minlength: "La contraseña debe tener al menos 8 caracteres"
                    },
                    password_confirmation: {
                        required: "Por favor confirma tu contraseña",
                        equalTo: "Las contraseñas no coinciden"
                    },
                },
                errorClass: "text-red-500",
                errorElement: "div",
                
            });
        });
    </script>
</body>
</html>