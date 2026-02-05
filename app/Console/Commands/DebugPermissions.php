<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class DebugPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:debug
                           {route? : Specific route to debug}
                           {--user= : User ID to check permissions for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug permission issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
         $user = $this->option('user')
            ? \App\Models\User::find($this->option('user'))
            : \App\Models\User::role('employer')->first();

        if (!$user) {
            $this->error('No super-admin user found!');
            return;
        }

        $this->info("Checking permissions for user: {$user->email} (ID: {$user->id})");
        $this->info("Roles: " . $user->getRoleNames()->implode(', '));
        $this->line('');

        // Get all admin routes
        $routes = Route::getRoutes()->getRoutes();
        $adminRoutes = [];

        foreach ($routes as $route) {
            $name = $route->getName();

            if (!$name || !str_starts_with($name, 'admin.')) {
                continue;
            }

            // Convert route to permission using your middleware logic
            $permissionName = \App\Services\PermissionService::routeToPermission($name);

            $adminRoutes[] = [
                'route' => $name,
                'permission' => $permissionName,
                'has_permission' => $user->can($permissionName),
                'exists' => Permission::where('name', $permissionName)->exists(),
            ];
        }

        // Display results
        $this->table(
            ['Route', 'Permission Name', 'Exists in DB', 'User Has Permission'],
            array_map(function ($item) {
                return [
                    $item['route'],
                    $item['permission'],
                    $item['exists'] ? '✅ Yes' : '❌ No',
                    $item['has_permission'] ? '✅ Yes' : '❌ No',
                ];
            }, $adminRoutes)
        );

        // Summary
        $total = count($adminRoutes);
        $hasPermission = count(array_filter($adminRoutes, fn($item) => $item['has_permission']));
        $existsInDB = count(array_filter($adminRoutes, fn($item) => $item['exists']));

        $this->line('');
        $this->info("📊 Summary:");
        $this->line("Total admin routes: {$total}");
        $this->line("Routes with permission in DB: {$existsInDB}");
        $this->line("Routes user has permission for: {$hasPermission}");
        $this->line("Missing permissions: " . ($total - $existsInDB));

        if ($existsInDB < $total) {
            $this->error('Some permissions are missing from the database!');
            $this->line('Run: php artisan jiattach:setup-permissions --all');
        }
    }
}
