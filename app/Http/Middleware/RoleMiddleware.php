<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $sessionUserId = (int) session('user.id');
        if ($sessionUserId <= 0) {
            return redirect('/auth')->with('error', 'Please login first.');
        }

        // Re-read the account on every protected request so a role change or
        // deactivation takes effect even when an older session is still open.
        $user = User::query()->find($sessionUserId);
        if (!$user || $user->role !== $role || !(bool) ($user->is_active ?? true)) {
            session()->forget('user');
            return redirect('/auth')->with('error', 'Your account no longer has access to this portal. Please contact the system administrator.');
        }

        if ($role === 'volunteer' && ($user->volunteer_status ?? 'Active') !== 'Active') {
            session()->forget('user');
            return redirect('/auth')->with('error', 'Your volunteer account is inactive. Please contact the organizer.');
        }

        // Keep session identity in sync with the database.
        session(['user.name' => $user->name, 'user.email' => $user->email, 'user.role' => $user->role]);

        return $next($request);
    }
}
