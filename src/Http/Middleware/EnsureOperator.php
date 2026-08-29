<?php

namespace Electrik\Http\Middleware;

use Closure;
use Electrik\Support\Operators;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOperator
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(Operators::check($request->user()), 403);

        return $next($request);
    }
}
