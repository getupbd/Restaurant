<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();

        if ($tenant && !$tenant->isSubscribed()) {
            if ($request->routeIs('filament.tenant.pages.billing') || $request->routeIs('*login') || $request->routeIs('*logout')) {
                return $next($request);
            }

            return redirect()->route('filament.tenant.pages.billing');
        }

        return $next($request);
    }
}
