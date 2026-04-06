<?php

namespace Tests\Feature\Catalog;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_users_catalog(): void
    {
        $userRole = Role::query()->create(['name' => 'user']);

        $user = User::factory()->create([
            'role_id' => $userRole->id,
        ]);

        Passport::actingAs($user, [], 'api');

        $response = $this->getJson('/api/admin/users');

        $response->assertForbidden();
    }

    public function test_admin_can_list_users_catalog(): void
    {
        $adminRole = Role::query()->create(['name' => 'admin']);
        $userRole = Role::query()->create(['name' => 'user']);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        User::factory()->create([
            'name' => 'Maria Perez',
            'role_id' => $userRole->id,
        ]);

        Passport::actingAs($admin, [], 'api');

        $response = $this->getJson('/api/admin/users');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'email',
                        'role',
                    ],
                ],
            ]);
    }

    public function test_admin_can_update_user_role_and_company(): void
    {
        $adminRole = Role::query()->create(['name' => 'admin']);
        $userRole = Role::query()->create(['name' => 'user']);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        $company = Company::query()->create([
            'name' => 'Empresa Demo',
        ]);

        $targetUser = User::factory()->create([
            'role_id' => null,
            'company_id' => null,
        ]);

        Passport::actingAs($admin, [], 'api');

        $response = $this->patchJson('/api/admin/users/'.$targetUser->id, [
            'role_id' => $userRole->id,
            'company_id' => $company->id,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.role.id', $userRole->id)
            ->assertJsonPath('data.company.id', $company->id);

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'role_id' => $userRole->id,
            'company_id' => $company->id,
        ]);
    }
}
