<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

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

        $dummyUser = User::firstOrCreate(
            ['email' => 'dummy@example.com'],
            [
                'name' => 'Dummy User',
                'email' => 'dummy@example.com',
                'password' => Hash::make('password'),
            ]
        );

        if (! $dummyUser->hasRole('admin')) {
            $dummyUser->assignRole($adminRole);
        }

        Artisan::call('permission:generate');
        Artisan::call('permission:discover');
    }
}
