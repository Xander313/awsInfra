<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Revelar Credenciales - SGPD COAC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #f3f4f6; }
    </style>
</head>
<body class="antialiased min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden m-4">
        <div class="bg-blue-900 p-6 text-center">
            <h1 class="text-white text-xl font-bold">SGPD COAC</h1>
            <p class="text-blue-200 text-sm mt-1">Seguridad de la Información</p>
        </div>

        <div class="p-8 text-center">
            <div class="mb-6">
                <div class="mx-auto w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Sus Credenciales</h2>
                <p class="text-gray-600 text-sm">
                    Esta información es confidencial. Cópiela ahora, ya que desaparecerá permanentemente al cerrar esta ventana.
                </p>
            </div>
            
            <div class="bg-gray-50 border-2 border-dashed border-gray-300 p-4 rounded-lg mb-6 relative group">
                <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Contraseña</p>
                <span class="text-3xl font-mono font-bold text-blue-800 tracking-wider select-all break-all" id="secretPass">
                    {{ $password }}
                </span>
            </div>

            <button onclick="copyToClipboard()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                </svg>
                Copiar Contraseña
            </button>
        </div>
        
        <div class="bg-red-50 p-4 text-center border-t border-red-100">
            <p class="text-red-600 italic flex items-center justify-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Enlace de un solo uso. No recargue la página.
            </p>
        </div>
    </div>

    <script>
    function copyToClipboard() {
        const text = document.getElementById('secretPass').innerText.trim();
        navigator.clipboard.writeText(text).then(() => {
            // Feedback visual simple
            const btn = document.querySelector('button');
            const originalText = btn.innerHTML;
            btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            btn.classList.add('bg-green-600', 'hover:bg-green-700');
            btn.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                ¡Copiado!
            `;
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                btn.classList.remove('bg-green-600', 'hover:bg-green-700');
            }, 2000);
        });
    }
    </script>
</body>
</html>