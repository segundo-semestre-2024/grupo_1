<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Rol
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $newRole = explode('|', $roles);
        $roleName = strtolower($request->user()->role->label);
        if (!in_array($roleName, $newRole)) 
            return abort(403,__('Unauthorized'));
        return $next($request);
    }
}
