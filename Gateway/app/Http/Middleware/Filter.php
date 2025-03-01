<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Filter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Leer la clave API desde el encabezado de la solicitud
        $apiKey = $request->header('X-API-KEY');
        $expectedApiKey = env('API-KEY'); // Leer la clave esperada desde el .env

        if ($apiKey !== $expectedApiKey) {
            return response()->json([
                'message' => 'Acceso denegado. Clave API incorrecta.'
            ], 403);
        }

        return $next($request); // Continuar con la solicitud si la clave es correcta
    }
}
