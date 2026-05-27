<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LibrarianMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user || (! $user->isAdmin() && ! $user->isLibrarian())) {
            abort(403, 'Acesso restrito a bibliotecários.');
        }

        return $next($request);
    }
}
