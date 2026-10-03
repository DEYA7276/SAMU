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
     * @param  string ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'No autenticado.',
            ], 401);
        }

        $flatRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $r) {
                $flatRoles[] = trim($r);
            }
        }

        if (! in_array($user->rol, $flatRoles, true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Acceso denegado: no cuentas con los permisos requeridos para esta acción.',
                'required_roles' => $flatRoles,
                'user_role' => $user->rol,
            ], 403);
        }

        return $next($request);
    }
}
