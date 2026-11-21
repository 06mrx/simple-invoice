<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        Artisan::call('permission:generate');
        Artisan::call('permission:discover');

        Permission::where('name', 'like', '%-installationitem')->delete();

        $invoiceAdminRole = Role::firstOrCreate(['name' => 'admin-invoice']);
        $invoiceAdminRole->syncPermissions(Permission::whereIn('name', [
            'view-customer',
            'create-customer',
            'edit-customer',
            'delete-customer',
            'view-purchaseorder',
            'create-purchaseorder',
            'edit-purchaseorder',
            'delete-purchaseorder',
            'view-invoice',
            'create-invoice',
            'edit-invoice',
            'delete-invoice',
            'send-invoice',
            'view-invoicesetting',
            'edit-invoicesetting',
        ])->get());

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@template.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@template.com',
                'password' => Hash::make('password'),
            ]
        );

        if (! $adminUser->hasRole('admin')) {
            $adminUser->assignRole($adminRole);
        }

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions(
            Permission::where('name', 'view-bayi')->get()
        );

        $dummyUser = User::firstOrCreate(
            ['email' => 'dummy@example.com'],
            [
                'name' => 'Dummy User',
                'email' => 'dummy@example.com',
                'password' => Hash::make('password'),
            ]
        );

        foreach ($dummyUser->getRoleNames() as $roleName) {
            if ($roleName !== 'user') {
                $dummyUser->removeRole($roleName);
            }
        }

        if (! $dummyUser->hasRole('user')) {
            $dummyUser->assignRole($userRole);
        }

        $this->call(InvoiceSettingSeeder::class);
    }
}
