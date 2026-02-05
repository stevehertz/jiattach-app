<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use App\Services\PermissionService;
use Spatie\Permission\Models\Permission;

class SetupPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jiattach:setup-permissions
                            {--scan-routes : Scan routes to generate permissions}
                            {--scan-models : Scan models to generate permissions}
                            {--assign : Assign default permissions to roles}
                            {--all : Perform all actions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup and manage permissions for Jiattach platform';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->info('Setting up Jiattach permissions and roles...');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $service = app(PermissionService::class);

        // Determine which actions to perform
        $scanRoutes = $this->option('scan-routes') || $this->option('all');
        $scanModels = $this->option('scan-models') || $this->option('all');
        $assignPermissions = $this->option('assign') || $this->option('all');

        $permissions = [];

        // Scan routes for permissions
        if ($scanRoutes) {
            $this->info('🔍 Scanning routes...');
            $routePermissions = $service->generateFromRoutes();
            $permissions = array_merge($permissions, $routePermissions);
            $this->info('✅ Found ' . count($routePermissions) . ' permissions from routes');
        }

        // Scan models for permissions
        if ($scanModels) {
            $this->info('🔍 Scanning models...');
            $modelPermissions = $service->generateFromModels();
            $permissions = array_merge($permissions, $modelPermissions);
            $this->info('✅ Found ' . count($modelPermissions) . ' permissions from models');
        }

        // Sync permissions with database
        if (!empty($permissions)) {
            $this->info('🔄 Syncing permissions with database...');
            $result = $service->syncPermissions($permissions);

            $this->table(['Type', 'Count'], [
                ['Total Permissions', $result['total']],
                ['New Permissions', count($result['created'])],
                ['Updated Permissions', count($result['updated'])],
            ]);

            if (!empty($result['created'])) {
                $this->info('New permissions:');
                $this->table(['Permission'], array_map(fn($p) => [$p], $result['created']));
            }
        }

        // Assign default permissions to roles
        if ($assignPermissions) {
            $this->info('👥 Assigning default permissions to roles...');
            $results = $service->assignDefaultPermissions();

            $this->table(
                ['Role', 'Permissions Assigned'],
                array_map(
                    fn($role, $count) => [$role, $count],
                    array_keys($results),
                    array_values($results)
                )
            );
        }

        // Show summary
        $this->showSummary();

        $this->info('✅ Permissions and roles setup completed!');
    }

    /**
     * Show summary of permissions and roles
     */
    private function showSummary(): void
    {
        $totalPermissions = Permission::count();
        $totalRoles = Role::count();
        
        $rolesWithCount = Role::withCount('permissions')->get()
            ->map(fn($role) => [$role->name, $role->permissions_count])
            ->toArray();

        $this->newLine();
        $this->info('📊 Summary:');
        $this->table(['Total Permissions', 'Total Roles'], [[$totalPermissions, $totalRoles]]);
        
        $this->info('👥 Roles with permission counts:');
        $this->table(['Role', 'Permissions'], $rolesWithCount);
    }
    
    private function createRoles()
    {
        $this->info('Creating roles and assigning permissions...');

        $rolePermissions = [
            'super-admin' => [
                'permissions' => Permission::all()->pluck('name')->toArray(),
            ],
            'admin' => [
                'permissions' => [
                    'view users', 'create users', 'edit users', 'delete users', 'manage user roles',
                    'verify users', 'verify employers', 'verify mentors', 'verify entrepreneurs',
                    'view all opportunities', 'approve opportunities',
                    'view all applications', 'manage applications',
                    'view all mentorships', 'manage mentorships',
                    'view all exchange programs', 'manage exchange programs', 'approve exchange programs',
                    'manage content', 'view reports', 'export reports'
                ],
            ],
            'moderator' => [
                'permissions' => [
                    'view users', 'verify users', 'verify employers', 'verify mentors', 'verify entrepreneurs',
                    'view all opportunities', 'approve opportunities',
                    'view all applications', 'manage applications',
                    'view all mentorships', 'manage mentorships',
                    'view all exchange programs', 'approve exchange programs',
                    'manage content'
                ],
            ],
            'student' => [
                'permissions' => [
                    'apply for opportunities', 'apply for mentorship', 'join exchange programs'
                ],
            ],
            'employer' => [
                'permissions' => [
                    'post opportunities', 'manage own opportunities'
                ],
            ],
            'mentor' => [
                'permissions' => [
                    'offer mentorship'
                ],
            ],
            'entrepreneur' => [
                'permissions' => [
                    'create exchange program', 'join exchange programs'
                ],
            ],
        ];

        $bar = $this->output->createProgressBar(count($rolePermissions));
        $bar->start();

        foreach ($rolePermissions as $roleName => $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web'],
                ['name' => $roleName, 'guard_name' => 'web']
            );

            $role->syncPermissions($roleData['permissions']);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('✅ ' . Role::count() . ' roles created/verified.');
    }
}
