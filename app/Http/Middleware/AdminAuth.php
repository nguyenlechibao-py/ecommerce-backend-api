<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::guard('admins')->check()) {
            return $next($request);
        }
        return response()->json(['is_success' => false, 'message' => 'Unauthorized, you don\'t have permission to access this route'], 403);
    }
}
