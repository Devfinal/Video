<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminPanelMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!($request->user()->hasRole('admin') || $request->user()->hasRole('model'))) {
            return redirect()->back();
        }

        return $next($request);
    }
}
