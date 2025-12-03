<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_if(!$request->user() || !$request->user()->is_admin, 403, 'Je hebt geen toegang tot deze pagina.');

        return $next($request);
    }
}
