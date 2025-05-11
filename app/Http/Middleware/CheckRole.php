<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Verificar si el usuario está autenticado
        if (!$request->user()) {
            return redirect()->route('login')
                ->with('error', 'Debes iniciar sesión para acceder a esta sección');
        }
        
        // Normalizar el rol recibido y el rol del usuario para evitar problemas de mayúsculas/minúsculas
        $role = strtolower(trim($role));
        $userRole = strtolower(trim($request->user()->role ?? ''));

        if ($userRole !== $role) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No autorizado'], 403);
            }
            
            // Redirigir según el rol del usuario
            if ($userRole === 'admin') {
                return redirect()->route('inventario.index')
                    ->with('error', 'No tienes acceso a esta sección. Tu rol actual es: ' . $request->user()->role);
            } else {
                return redirect()->route('tienda.index')
                    ->with('error', 'No tienes acceso a esta sección. Tu rol actual es: ' . $request->user()->role);
            }
        }

        return $next($request);
    }
}
