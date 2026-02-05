<?php

namespace App\Traits;

trait MapsRouteToPermission
{
    /**
     * Convert a Laravel route name to a standardized Spatie permission name.
     * Example: 'admin.users.index' -> 'view users'
     * Example: 'admin.dashboard' -> 'view dashboard'
     */
    public static function routeToPermission(string $routeName): string
    {
        if (empty($routeName)) {
            return '';
        }

        $parts = explode('.', $routeName);

        // 1. Remove 'admin' prefix
        if ($parts[0] === 'admin') {
            array_shift($parts);
        }

        // 2. Handle Resource Routes (e.g., admin.users.index)
        if (count($parts) >= 2) {
            $action = array_pop($parts); // index, show, edit, etc.
            $resource = implode(' ', $parts); // users, exchange-programs, etc.

            // Map Laravel resource actions to Permission verbs
            $actionMap = [
                'index'        => 'view',
                'show'         => 'view',
                'create'       => 'create',
                'store'        => 'create',
                'edit'         => 'edit',
                'update'       => 'edit',
                'destroy'      => 'delete',
                'trash'        => 'delete',
                'restore'      => 'restore',
                'forceDelete'  => 'force delete',
                'export'       => 'export',
                'import'       => 'import',
                'bulk-action'  => 'bulk action',
                // Custom filters often found in your debug table:
                'active'       => 'view',
                'verified'     => 'view',
                'pending'      => 'view',
                'completed'    => 'view',
            ];

            $permissionAction = $actionMap[$action] ?? $action;

            // Clean up the resource name (convert hyphens to spaces)
            $resource = str_replace('-', ' ', $resource);

            return "{$permissionAction} {$resource}";
        }

        // 3. Handle Single-word Routes (e.g., admin.dashboard, admin.help)
        // We prefix with 'view' to keep it consistent (e.g., 'view dashboard')
        $name = str_replace('-', ' ', implode(' ', $parts));
        return "view {$name}";
    }
}
