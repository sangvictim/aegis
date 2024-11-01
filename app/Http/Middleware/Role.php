<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next): Response
    {
        $userRole = auth()->user()->role;
        $url = $request->path();

        if ($userRole == 'admin') {
            return $next($request);
        }

        // url yang boleh diakses oleh kasir
        if ($userRole == 'kasir' && in_array($url, [
            'api/sales/order',
            'api/sales/list'
        ])) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Unauthorized'
        ], 401);
    }
}
