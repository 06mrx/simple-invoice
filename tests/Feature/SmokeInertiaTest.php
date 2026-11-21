<?php

namespace Tests\Feature;

use App\Models\Bayi;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SmokeInertiaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminSeeder::class);
    }

    public function test_admin_pages_render(): void
    {
        $user = User::where('email', 'admin@template.com')->first();
        $this->actingAs($user);

        $bayi = Bayi::create([
            'id' => (string) Str::uuid(),
            'nama' => 'Test Bayi',
            'nik' => '1234567890',
            'tgl_lahir' => '2024-01-01',
            'jk' => 'L',
            'nama_ortu' => 'Ortu',
        ]);

        $role = Role::first();
        $permission = Permission::first();

        $this->get(route('bayi.index'))->assertOk();
        $this->get(route('bayi.create'))->assertOk();
        $this->get(route('bayi.edit', $bayi->id))->assertOk();

        $this->get(route('admin.users.index'))->assertOk();
        $this->get(route('admin.users.create'))->assertOk();
        $this->get(route('admin.users.edit', $user->id))->assertOk();

        $this->get(route('admin.roles.index'))->assertOk();
        $this->get(route('admin.roles.create'))->assertOk();
        $this->get(route('admin.roles.edit', $role->id))->assertOk();

        $this->get(route('admin.permissions.index'))->assertOk();
        $this->get(route('admin.permissions.create'))->assertOk();
        $this->get(route('admin.permissions.edit', $permission->id))->assertOk();

        $this->get(route('admin.audit.index'))->assertOk();
    }

    public function test_non_admin_user_has_limited_permissions(): void
    {
        $user = User::where('email', 'dummy@example.com')->first();
        $this->actingAs($user);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('auth.roles', ['user'])
                ->where('auth.permissions', ['view-bayi']));

        $this->get(route('bayi.index'))->assertOk();
        $this->get(route('admin.users.index'))->assertForbidden();
        $this->get(route('admin.roles.index'))->assertForbidden();
        $this->get(route('admin.permissions.index'))->assertForbidden();
    }
}
