<?php

namespace Tests\Feature\Catalog;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class RoleCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_roles_catalog(): void
    {
        $userRole = Role::query()->create(['name' => 'user']);

        $user = User::factory()->create([
            'role_id' => $userRole->id,
        ]);

        Passport::actingAs($user, [], 'api');

        $response = $this->getJson('/api/admin/roles');

        $response->assertForbidden();
    }

    public function test_admin_can_list_roles_catalog(): void
    {
        $adminRole = Role::query()->create(['name' => 'admin']);
        Role::query()->create(['name' => 'user']);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        Passport::actingAs($admin, [], 'api');

        $response = $this->getJson('/api/admin/roles');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    ['id', 'name'],
                ],
            ]);
    }
}
