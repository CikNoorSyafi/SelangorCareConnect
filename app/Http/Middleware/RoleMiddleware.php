<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string $role
    ): Response {

        if (!session()->has('user')) {

            return redirect('/auth')
                ->with(
                    'error',
                    'Please login first.'
                );
        }

        if (
            session('user.role')
            !=
            $role
        ) {

            abort(403);
        }

        return $next($request);
    }
}