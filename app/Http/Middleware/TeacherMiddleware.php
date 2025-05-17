<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TeacherMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['teacher', 'admin'])) {
            return redirect()->route('home')->with('error', 'Unauthorized access. Teacher privileges required.');
        }

        return $next($request);
    }
}
