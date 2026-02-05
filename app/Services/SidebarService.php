<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class SidebarService
{
     /**
     * Get all admin routes grouped by section
     */
    public function getAdminRoutes(): array
    {
        return Cache::remember('admin.sidebar.routes', 3600, function () {
            $routes = Route::getRoutes()->getRoutes();
            $adminRoutes = [];

            foreach ($routes as $route) {
                $name = $route->getName();

                // Skip if not admin route
                if (!$name || !Str::startsWith($name, 'admin.')) {
                    continue;
                }

                // Skip if it's a wildcard route (contains {parameter})
                if (Str::contains($route->uri, '{') && !Str::contains($route->uri, 'show') && !Str::contains($route->uri, 'edit')) {
                    continue;
                }

                // Parse route parts
                $parts = explode('.', $name);
                array_shift($parts); // Remove 'admin' prefix

                // Skip if no further parts
                if (empty($parts)) {
                    continue;
                }

                // Get the main resource (first part)
                $resource = $parts[0];
                $action = count($parts) > 1 ? end($parts) : 'index';

                // Skip non-standard actions
                if (!in_array($action, [
                    'index',
                    'create',
                    'active',
                    'pending',
                    'verified',
                    'featured',
                    'available',
                    'seeking',
                    'on-attachment',
                    'open',
                    'ongoing',
                    'completed',
                    'virtual',
                    'in-person',
                    'hybrid', 'analytics', 'reports', 'general', 'email', 'payment', 'notifications', 'security', 'backup'])) {
                    continue;
                }

                // Determine section based on resource
                $section = $this->getSectionForResource($resource);

                // Determine display name
                $displayName = $this->getDisplayName($resource, $action);

                // Determine icon
                $icon = $this->getIconForResource($resource, $action);

                // Determine if it's a parent or child
                $isParent = $this->isParentRoute($resource, $action);

                // Get permission name
                $permissionName = $this->routeToPermission($name);

                $adminRoutes[$section][$resource][] = [
                    'name' => $name,
                    'display_name' => $displayName,
                    'icon' => $icon,
                    'is_parent' => $isParent,
                    'action' => $action,
                    'resource' => $resource,
                    'permission' => $permissionName,
                    'uri' => $route->uri(),
                ];
            }

            // Sort sections
            $sections = [
                'dashboard' => 'Dashboard',
                'user_management' => 'USER MANAGEMENT',
                'opportunities_programs' => 'OPPORTUNITIES & PROGRAMS',
                'mentorship' => 'MENTORSHIP',
                'reports_analytics' => 'REPORTS & ANALYTICS',
                'system_settings' => 'SYSTEM SETTINGS',
                'quick_links' => 'QUICK LINKS',
            ];

            $sortedRoutes = [];
            foreach ($sections as $key => $label) {
                if (isset($adminRoutes[$key])) {
                    $sortedRoutes[$key] = [
                        'label' => $label,
                        'routes' => $adminRoutes[$key],
                    ];
                }
            }

            return $sortedRoutes;
        });
    }

     /**
     * Get section for resource
     */
    private function getSectionForResource(string $resource): string
    {
        return match($resource) {
            'dashboard' => 'dashboard',
            'users', 'students', 'employers', 'mentors', 'administrators', 'roles' => 'user_management',
            'opportunities', 'applications', 'exchange-programs' => 'opportunities_programs',
            'mentorships' => 'mentorship',
            'reports' => 'reports_analytics',
            'settings', 'activity-logs', 'system-health', 'database' => 'system_settings',
            'help', 'documentation', 'profile' => 'quick_links',
            default => 'system_settings',
        };
    }

    /**
     * Get display name for route
     */
    private function getDisplayName(string $resource, string $action): string
    {
        $resourceNames = [
            'users' => 'Users',
            'students' => 'Students',
            'employers' => 'Employers',
            'mentors' => 'Mentors',
            'administrators' => 'Administrators',
            'roles' => 'Roles',
            'opportunities' => 'Opportunities',
            'applications' => 'Applications',
            'exchange-programs' => 'Exchange Programs',
            'mentorships' => 'Mentorship',
            'reports' => 'Reports',
            'settings' => 'Settings',
            'activity-logs' => 'Activity Logs',
            'system-health' => 'System Health',
            'database' => 'Database',
            'help' => 'Help',
            'documentation' => 'Documentation',
            'profile' => 'Profile',
        ];

        $actionNames = [
            'index' => 'All',
            'create' => 'Create New',
            'active' => 'Active',
            'pending' => 'Pending',
            'verified' => 'Verified',
            'featured' => 'Featured',
            'available' => 'Available',
            'seeking' => 'Seeking Attachment',
            'on-attachment' => 'On Attachment',
            'open' => 'Open',
            'ongoing' => 'Ongoing',
            'completed' => 'Completed',
            'virtual' => 'Virtual',
            'in-person' => 'In-person',
            'hybrid' => 'Hybrid',
            'analytics' => 'Analytics',
            'reports' => 'Reports',
            'general' => 'General',
            'email' => 'Email',
            'payment' => 'Payment',
            'notifications' => 'Notifications',
            'security' => 'Security',
            'backup' => 'Backup',
        ];

        $resourceName = $resourceNames[$resource] ?? ucfirst(str_replace('-', ' ', $resource));
        $actionName = $actionNames[$action] ?? ucfirst($action);

        if ($action === 'index') {
            return $resourceName;
        }

        return $actionName . ' ' . $resourceName;
    }

    /**
     * Get icon for resource
     */
    private function getIconForResource(string $resource, string $action): string
    {
        $icons = [
            'dashboard' => 'tachometer-alt',
            'users' => 'users',
            'students' => 'user-graduate',
            'employers' => 'building',
            'mentors' => 'user-tie',
            'administrators' => 'user-shield',
            'roles' => 'user-shield',
            'opportunities' => 'briefcase',
            'applications' => 'file-alt',
            'exchange-programs' => 'exchange-alt',
            'mentorships' => 'hands-helping',
            'reports' => 'chart-line',
            'settings' => 'cog',
            'activity-logs' => 'history',
            'system-health' => 'heartbeat',
            'database' => 'database',
            'help' => 'question-circle',
            'documentation' => 'book',
            'profile' => 'user',
        ];

        return $icons[$resource] ?? 'circle';
    }

    /**
     * Check if route should be a parent
     */
    private function isParentRoute(string $resource, string $action): bool
    {
        // Only index routes with children become parents
        if ($action !== 'index') {
            return false;
        }

        // These resources have sub-menus
        $parentResources = [
            'students',
            'employers',
            'mentors',
            'administrators',
            'roles',
            'opportunities',
            'applications',
            'exchange-programs',
            'mentorships',
            'reports',
            'settings',
        ];

        return in_array($resource, $parentResources);
    }

    /**
     * Convert route name to permission name (matches your middleware)
     */
    private function routeToPermission(string $routeName): string
    {
        $parts = explode('.', $routeName);

        if ($parts[0] === 'admin') {
            array_shift($parts);
        }

        if (count($parts) >= 2) {
            $action = array_pop($parts);
            $resource = implode(' ', $parts);

            $actionMap = [
                'index' => 'view',
                'show' => 'view',
                'create' => 'create',
                'store' => 'create',
                'edit' => 'edit',
                'update' => 'edit',
                'destroy' => 'delete',
            ];

            $permissionAction = $actionMap[$action] ?? $action;
            return "{$permissionAction} {$resource}";
        }

        return implode(' ', $parts);
    }

    /**
     * Get sidebar menu with permissions check
     */
    public function getSidebarMenu($user): array
    {
        $routes = $this->getAdminRoutes();
        $menu = [];

        foreach ($routes as $sectionKey => $sectionData) {
            $sectionItems = [];

            foreach ($sectionData['routes'] as $resource => $resourceRoutes) {
                $parentRoute = null;
                $children = [];

                foreach ($resourceRoutes as $route) {
                    // Check if user has permission for this route
                    if (!$user->can($route['permission'])) {
                        continue;
                    }

                    if ($route['is_parent']) {
                        $parentRoute = $route;
                    } else {
                        $children[] = $route;
                    }
                }

                // If there's a parent with children, create parent item
                if ($parentRoute && !empty($children)) {
                    $sectionItems[] = [
                        'type' => 'parent',
                        'route' => $parentRoute,
                        'children' => $children,
                    ];
                }
                // If only children without parent (shouldn't happen)
                elseif (!empty($children) && empty($parentRoute)) {
                    foreach ($children as $child) {
                        $sectionItems[] = [
                            'type' => 'single',
                            'route' => $child,
                        ];
                    }
                }
                // If only parent without children (like dashboard)
                elseif ($parentRoute && empty($children)) {
                    $sectionItems[] = [
                        'type' => 'single',
                        'route' => $parentRoute,
                    ];
                }
            }

            if (!empty($sectionItems)) {
                $menu[$sectionKey] = [
                    'label' => $sectionData['label'],
                    'items' => $sectionItems,
                ];
            }
        }

        return $menu;
    }

    /**
     * Get statistics for sidebar badges
     */
    public function getStatistics(): array
    {
        return [];
    }
}
