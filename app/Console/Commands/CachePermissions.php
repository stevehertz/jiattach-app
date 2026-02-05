<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;

class CachePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:cache 
                            {--clear : Clear the permission cache}
                            {--preload : Preload permissions for faster access}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache permissions for faster access';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        if ($this->option('clear')) {
            Cache::forget('permissions.all');
            Cache::forget('permissions.grouped');
            Cache::forget('permissions.routes');
            $this->info('✅ Permission cache cleared.');
            return;
        }

        if ($this->option('preload')) {
            $this->preloadPermissions();
            return;
        }

        // Cache all permissions
        $permissions = Permission::with('roles')->get();
        Cache::forever('permissions.all', $permissions);

        // Cache grouped permissions
        $grouped = [];
        foreach ($permissions as $permission) {
            $parts = explode(' ', $permission->name);
            $resource = count($parts) >= 2 ? $parts[1] : 'other';
            $grouped[$resource][] = $permission;
        }
        Cache::forever('permissions.grouped', $grouped);

        $this->info('✅ Permissions cached successfully.');
        $this->info('   Total: ' . $permissions->count());
        $this->info('   Groups: ' . count($grouped));
    }

    /**
     * Preload permissions for faster middleware access
     */
    private function preloadPermissions(): void
    {
        $this->info('🔄 Preloading permissions...');
        
        // Load all permissions with roles
        Permission::with('roles')->get()->each(function ($permission) {
            // This ensures permissions are loaded into memory
            $permission->roles->each->name;
        });
        
        // Load all roles with permissions
        \Spatie\Permission\Models\Role::with('permissions')->get()->each(function ($role) {
            $role->permissions->each->name;
        });
        
        $this->info('✅ Permissions preloaded in memory.');
    }
}
