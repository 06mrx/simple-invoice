<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DiscoverPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:discover';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto discover module permissions and assign to "admin" role';

    /**
     * Map method to permission action.
     *
     * @var array
     */
    protected $actionMap = [
        'index' => 'view',
        'show' => 'view',
        'create' => 'create',
        'store' => 'create',
        'edit' => 'edit',
        'update' => 'edit',
        'destroy' => 'delete',
        'send' => 'send',
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔍 Discovering permissions from Admin routes...');

        $routes = \Route::getRoutes();

        foreach ($routes as $route) {
            $action = $route->getAction();

            if (! isset($action['controller']) || ! str_starts_with($action['controller'], 'App\\Http\\Controllers\\')) {
                continue;
            }

            $routeName = $route->getName();
            $prefixes = ['admin.', 'customers.', 'purchase-orders.', 'invoices.', 'invoice-settings.'];
            if (! $routeName || ! collect($prefixes)->contains(fn (string $prefix) => str_starts_with($routeName, $prefix))) {
                continue;
            }

            [$controllerClass, $method] = explode('@', $action['controller']);
            $controllerName = class_basename($controllerClass);
            $resource = strtolower(str_replace('Controller', '', $controllerName));

            // Jika method tidak ada di map, skip
            if (! array_key_exists($method, $this->actionMap)) {
                continue;
            }

            $actionVerb = $this->actionMap[$method];

            // Buat nama permission: view-user, create-role, dll
            $permissionName = $actionVerb.'-'.$resource;

            // Simpan permission jika belum ada
            $permission = Permission::firstOrCreate(['name' => $permissionName]);

            $this->line("✅ Created/Found: <fg=green>{$permissionName}</>");
        }

        $this->info('✔️ Permissions discovery completed.');

        // Assign semua permission ke role admin jika ada
        $adminRole = Role::whereName('admin')->first();
        if ($adminRole) {
            $permissions = Permission::all();
            $adminRole->syncPermissions($permissions);

            $this->info("🛡️ Assigned all permissions to 'admin' role.");
        } else {
            $this->warn("⚠️ Role 'admin' not found. Skipping permission assignment.");
        }

        return 0;
    }
}
