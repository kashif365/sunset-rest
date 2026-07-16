<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_access_menu_management(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)
            ->get(route('admin.categories.index'))
            ->assertForbidden();
    }

    public function test_staff_can_access_orders(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)
            ->get(route('admin.orders.index'))
            ->assertOk();
    }

    public function test_manager_can_access_menu_management(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->get(route('admin.categories.index'))
            ->assertOk();
    }

    public function test_manager_cannot_access_user_management(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_manager_cannot_access_business_settings(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->get(route('admin.settings.edit', 'business'))
            ->assertForbidden();
    }

    public function test_super_admin_can_access_everything(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)->get(route('admin.categories.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.settings.edit', 'business'))->assertOk();
    }

    public function test_super_admin_cannot_delete_own_account(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
