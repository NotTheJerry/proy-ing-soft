<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Verifica si el usuario tiene el rol especificado
     *
     * @param string $role
     * @return bool
     */
    protected function hasRole($role)
    {
        return Auth::check() && Auth::user()->role === $role;
    }

    /**
     * Asegura que el usuario tiene el rol especificado o aborta con error 403
     *
     * @param string $role
     * @return void
     */
    protected function requireRole($role)
    {
        if (!$this->hasRole($role)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }
}
