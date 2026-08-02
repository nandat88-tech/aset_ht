<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOperator
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            abort(403);
        }

        if (! in_array($request->user()->role, ['admin', 'operator'])) {
            abort(403, 'Halaman ini hanya untuk Admin atau Operator.');
        }

        return $next($request);
    }
}