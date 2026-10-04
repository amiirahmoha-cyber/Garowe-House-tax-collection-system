<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts access based on user role.
 *
 * Administrator -> full access to everything.
 * Collector / Viewer -> can ONLY see Dashboard, Owners, Properties, Payments.
 *   They are blocked from: Tax Records, Reports, Activity Logs, Users,
 *   SMS Reminders, and Settings (Property Classes/Types/Villages).
 *
 * Usage in routes: ->middleware('role:Administrator')
 */
class RoleMiddleware {
    public function handle(Request $request, Closure $next, string ...$roles): Response {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!in_array($user->role, $roles)) {
            abort(403, 'Ma haysatid ogolaansho aad ku gasho bogga kan. (You do not have permission to access this page.)');
        }

        return $next($request);
    }
}
