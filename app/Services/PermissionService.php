<?php

namespace App\Services;

use ReflectionClass;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Traits\MapsRouteToPermission;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    use MapsRouteToPermission; // Import the trait

    /**
     * Generate permissions from routes
     */
    public function generateFromRoutes(): array
    {
        $permissions = [];
        $routes = Route::getRoutes()->getRoutes();

        foreach ($routes as $route) {
            // Skip if no name or not admin route
            if (!$route->getName() || !Str::startsWith($route->getName(), 'admin.')) {
                continue;
            }

            $permissionName = self::routeToPermission($route->getName());
            $description = "Access to " . $route->uri();

            // Use permission name as key and description as value
            $permissions[$permissionName] = $description;
        }

        return $permissions;
    }

    /**
     * Generate CRUD permissions from models
     */
    public function generateFromModels(): array
    {
        $permissions = [];
        $modelPath = app_path('Models');
        $files = scandir($modelPath);

        foreach ($files as $file) {
            if (!Str::endsWith($file, '.php')) {
                continue;
            }

            $modelName = str_replace('.php', '', $file);
            $fullClassName = "App\\Models\\{$modelName}";

            // Skip if not a valid model class
            if (!class_exists($fullClassName) || $modelName === 'Permission' || $modelName === 'Role') {
                continue;
            }

            $modelPermissions = $this->getModelPermissions($modelName, $fullClassName);
            $permissions = array_merge($permissions, $modelPermissions);
        }

        return $permissions;
    }

    /**
     * Get description for route permission
     */
    protected function getRouteDescription(\Illuminate\Routing\Route $route): string
    {
        $uri = $route->uri();
        $methods = implode(', ', $route->methods());

        return "Access to {$uri} via {$methods}";
    }

    /**
     * Get standard CRUD permissions for a model
     */
    protected function getModelPermissions(string $modelName, string $className): array
    {
        $permissions = [];
        $snakeModel = Str::snake($modelName);
        $pluralModel = Str::plural($snakeModel);

        // Basic CRUD permissions
        $crudPermissions = [
            "view {$pluralModel}" => "View {$modelName} records",
            "create {$pluralModel}" => "Create new {$modelName} records",
            "edit {$pluralModel}" => "Edit existing {$modelName} records",
            "delete {$pluralModel}" => "Delete {$modelName} records",
            "restore {$pluralModel}" => "Restore deleted {$modelName} records",
            "force delete {$pluralModel}" => "Permanently delete {$modelName} records",
            "export {$pluralModel}" => "Export {$modelName} data",
            "import {$pluralModel}" => "Import {$modelName} data",
        ];

        // Model-specific permissions based on model analysis
        $reflection = new ReflectionClass($className);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $propertyName = $property->getName();

            // Add file upload permissions if property suggests file uploads
            if (Str::contains($propertyName, ['_url', '_path', 'file', 'image', 'logo', 'avatar'])) {
                $permissions["upload {$snakeModel} {$propertyName}"] = "Upload {$propertyName} for {$modelName}";
            }
        }

        // Check for SoftDeletes trait
        if (method_exists($className, 'bootSoftDeletes')) {
            $crudPermissions["view trashed {$pluralModel}"] = "View trashed {$modelName} records";
        }

        // Check for specific methods in model
        $modelInstance = app($className);

        // Verification-related permissions
        if (method_exists($modelInstance, 'verify') || property_exists($modelInstance, 'is_verified')) {
            $crudPermissions["verify {$pluralModel}"] = "Verify {$modelName} records";
            $crudPermissions["unverify {$pluralModel}"] = "Unverify {$modelName} records";
        }

        // Feature-related permissions
        if (method_exists($modelInstance, 'feature') || property_exists($modelInstance, 'is_featured')) {
            $crudPermissions["feature {$pluralModel}"] = "Feature {$modelName} records";
            $crudPermissions["unfeature {$pluralModel}"] = "Unfeature {$modelName} records";
        }

        // Publish-related permissions
        if (method_exists($modelInstance, 'publish') || property_exists($modelInstance, 'status')) {
            $crudPermissions["publish {$pluralModel}"] = "Publish {$modelName} records";
            $crudPermissions["unpublish {$pluralModel}"] = "Unpublish {$modelName} records";
            $crudPermissions["approve {$pluralModel}"] = "Approve {$modelName} records";
        }

        // Merge all permissions
        return array_merge($crudPermissions, $permissions);
    }

    /**
     * Sync permissions with database
     */
    public function syncPermissions(array $permissions): array
    {
        $created = [];
        $updated = [];
        $existingPermissions = Permission::pluck('name')->toArray();

        foreach ($permissions as $name => $description) {
            $permission = Permission::firstOrNew(['name' => $name]);

            if ($permission->exists) {
                // Update only the description if it changed
                if ($permission->description !== $description) {
                    $permission->description = $description;
                    $permission->save();
                    $updated[] = $name;
                }
            } else {
                $permission->guard_name = 'web';
                $permission->name = $name; // Use the permission name as key
                $permission->description = $description; // Store description separately
                $permission->save();
                $created[] = $name;
            }
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'total' => count($permissions),
        ];
    }

    /**
     * Clean up permissions not in the current list
     */
    protected function cleanupPermissions(array $currentPermissions): void
    {
        $orphanedPermissions = Permission::whereNotIn('name', $currentPermissions)->get();

        foreach ($orphanedPermissions as $permission) {
            // Check if any role uses this permission
            $usedByRoles = Role::whereHas('permissions', function ($query) use ($permission) {
                $query->where('permissions.id', $permission->id);
            })->exists();

            if (!$usedByRoles) {
                $permission->delete();
            }
        }
    }

    /**
     * Get all permissions grouped by resource
     */
    public function getGroupedPermissions(): array
    {
        $permissions = Permission::orderBy('name')->get();
        $grouped = [];

        foreach ($permissions as $permission) {
            $parts = explode(' ', $permission->name);

            if (count($parts) >= 2) {
                $resource = $parts[1];

                if (count($parts) > 2) {
                    $resource = implode(' ', array_slice($parts, 1, -1));
                }

                $grouped[$resource][] = [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'description' => $permission->description,
                    'action' => $parts[0],
                ];
            } else {
                $grouped['other'][] = [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'description' => $permission->description,
                    'action' => $permission->name,
                ];
            }
        }

        ksort($grouped);
        return $grouped;
    }

    /**
     * Assign default permissions to roles (FIXED)
     */
    public function assignDefaultPermissions(): array
    {
        $rolePermissions = [
            'super-admin' => Permission::all()->pluck('name')->toArray(),

            'admin' => [
                // User Management
                'view users',
                'create users',
                'edit users',
                'delete users',
                'manage user roles',
                'verify users',
                'verify employers',
                'verify mentors',
                'verify entrepreneurs',

                // Students
                'view students',
                'edit students',
                'delete students',

                // Employers
                'view employers',
                'edit employers',
                'verify employers',
                'unverify employers',

                // Mentors
                'view mentors',
                'edit mentors',
                'verify mentors',
                'unverify mentors',
                'feature mentors',
                'unfeature mentors',

                // Administrators
                'view administrators',
                'create administrators',
                'edit administrators',
                'delete administrators',

                // Opportunities
                'view opportunities',
                'create opportunities',
                'edit opportunities',
                'delete opportunities',
                'approve opportunities',
                'publish opportunities',
                'unpublish opportunities',

                // Applications
                'view applications',
                'edit applications',
                'delete applications',
                'manage applications',

                // Exchange Programs
                'view exchange_programs',
                'create exchange_programs',
                'edit exchange_programs',
                'delete exchange_programs',
                'approve exchange_programs',

                // Mentorships
                'view mentorships',
                'create mentorships',
                'edit mentorships',
                'delete mentorships',

                // Reports
                'view reports',
                'export reports',

                // Settings
                'manage settings',

                // Content
                'manage content',

                // System
                'view activity_logs',
                'view system_health',
                'access database',
            ],

            'moderator' => [
                'view users',
                'verify users',
                'view students',
                'edit students',
                'view employers',
                'verify employers',
                'view mentors',
                'verify mentors',
                'view opportunities',
                'approve opportunities',
                'view applications',
                'manage applications',
                'view exchange_programs',
                'approve exchange_programs',
                'view mentorships',
                'manage content',
            ],

            'student' => [
                'apply for opportunities',
                'apply for mentorship',
                'join exchange_programs',
                'view own profile',
                'edit own profile',
                'upload student_profile cv_url',
                'upload student_profile transcript_url',
            ],

            'employer' => [
                self::routeToPermission('admin.dashboard'),
                'post opportunities',
                'manage own opportunities',
                'view own profile',
                'edit own profile',
                'upload employer logo_url',
                'view own applications',
                'manage own applications',
            ],

            'mentor' => [
                'offer mentorship',
                'view own profile',
                'edit own profile',
                'upload mentor profile_picture',
                'view own mentorships',
                'manage own mentorships',
            ],

            'entrepreneur' => [
                'create exchange_program',
                'join exchange_programs',
                'view own profile',
                'edit own profile',
                'manage own exchange_programs',
            ],
        ];

        $results = [];

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            // Get actual permission IDs
            $permissions = Permission::whereIn('name', $permissionNames)->pluck('id')->toArray();
            $role->syncPermissions($permissions);

            $results[$roleName] = count($permissions);
        }

        return $results;
    }

    /**
     * Convert route to permission name (same as middleware)
     */
    public function routeToPermissionForMiddleware(string $routeName): string
    {
        return $this->routeToPermission($routeName);
    }

    /**
     * Get permission for a specific route
     */
    public function getPermissionForRoute(string $routeName): ?string
    {
        $permissionName = $this->routeToPermission($routeName);
        return Permission::where('name', $permissionName)->first()?->name;
    }
    
}