<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleValid
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$rol): Response
    {

        if (in_array($request->user()->role, $rol))
            {
                return $next($request);
            }
        else
            {
                abort(403, 'No Autorizado');
            }
    }

}
    