<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Core\Org;

class EnsureOrgSelected
{
    public function handle($request, Closure $next)
    {
        // Permitir rutas de org sin bloqueo
        if ($request->is('orgs*')) {
            return $next($request);
        }

        // Si no existe ninguna organización → forzar creación
        if (Org::count() === 0) {
            return redirect()->route('orgs.create');
        }

        // Si no hay org activa → ir al listado
        if (!session()->has('org_id')) {
            return redirect()->route('orgs.index')
                ->with('warning', 'Seleccione una organización.');
        }

        return $next($request);
    }
}
