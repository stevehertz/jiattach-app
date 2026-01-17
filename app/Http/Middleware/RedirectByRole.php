<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectByRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is not authenticated, continue
        if (!Auth::check()) {
            return $next($request);
        }

        $user = User::findOrFail(Auth::user()->id);
        $currentRoute = $request->route()->getName();

        // Assign student role if user has no role
        if (!$user->hasAnyRole(['super-admin', 'admin', 'moderator', 'student',  'employer', 'mentor'])) {
            $user->assignRole('student');
        }

         // Define dashboard routes
        $studentDashboard = 'student.dashboard';
        $adminDashboard = 'admin.dashboard'; // This will be for all non-student roles

        // Check if user is trying to access login/register when already logged in
        $authRoutes = ['login', 'register', 'password.request', 'password.reset'];
        if (in_array($currentRoute, $authRoutes)) {
            return $this->redirectToCorrectDashboard($user);
        }


        // Get user's primary role
        $userRoles = $user->getRoleNames();
        $primaryRole = $userRoles->first();

        if ($primaryRole === 'student') {

            // Allow student routes and general routes
            $allowedPrefixes = ['student.', 'logout'];

            // Check if current route starts with allowed prefix or is logout
            $isAllowed = false;

            if ($currentRoute) {
                foreach ($allowedPrefixes as $prefix) {
                    if (str_starts_with($currentRoute, $prefix) || $currentRoute === $prefix) {
                        $isAllowed = true;
                        break;
                    }
                }
            }

             // Also allow null routes (some routes might not have names)
            if (!$currentRoute) {
                $isAllowed = true;
            }

            // If trying to access admin routes, redirect to student dashboard
            if ($currentRoute && str_starts_with($currentRoute, 'admin.')) {
                return redirect()->route($studentDashboard);
            }

              // If accessing a non-student route (and it's not null), redirect to student dashboard
            if (!$isAllowed && $currentRoute !== null) {
                return redirect()->route($studentDashboard);
            }

        } else {
             // Allow admin routes and general routes
            $allowedPrefixes = ['admin.', 'logout'];

            // Check if current route starts with allowed prefix or is logout
            $isAllowed = false;
            if ($currentRoute) {
                foreach ($allowedPrefixes as $prefix) {
                    if (str_starts_with($currentRoute, $prefix) || $currentRoute === $prefix) {
                        $isAllowed = true;
                        break;
                    }
                }
            }

            // Also allow null routes
            if (!$currentRoute) {
                $isAllowed = true;
            }

             // If trying to access student routes, redirect to admin dashboard
            if ($currentRoute && str_starts_with($currentRoute, 'student.')) {
                return redirect()->route($adminDashboard);
            }

            if (!$isAllowed && $currentRoute !== null) {
                return redirect()->route($adminDashboard);
            }
        }

        return $next($request);
    }


    /**
     * Redirect user to correct dashboard based on role.
     */
    private function redirectToCorrectDashboard($user): Response
    {
        if ($user->hasRole('student')) {
            return redirect()->route('student.dashboard');
        } else {
            // All other roles (admin, employer, mentor) go to admin dashboard
            return redirect()->route('admin.dashboard');
        }
    }
}
