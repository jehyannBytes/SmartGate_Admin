<?php
// app/Http/Middleware/RoleMiddleware.php
// SmartGate ACC — Restrict routes based on admin role (hr / ictmo)

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes/web.php:
     *   ->middleware('role:hr')       // HR only
     *   ->middleware('role:ictmo')    // ICTMO only
     *   ->middleware('role:hr,ictmo') // Both roles allowed
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Must be logged in
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role;

        // Check if user's role is in the allowed roles list
        if (!in_array($userRole, $roles)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
