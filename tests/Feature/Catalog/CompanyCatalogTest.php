<?php

namespace Tests\Feature\Catalog;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CompanyCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_companies_catalog(): void
    {
        $userRole = Role::query()->create(['name' => 'user']);

        $user = User::factory()->create([
            'role_id' => $userRole->id,
        ]);

        Passport::actingAs($user, [], 'api');

        $response = $this->getJson('/api/admin/companies');

        $response->assertForbidden();
    }

    public function test_admin_can_create_and_delete_company(): void
    {
        $adminRole = Role::query()->create(['name' => 'admin']);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        Passport::actingAs($admin, [], 'api');

        $storeResponse = $this->postJson('/api/admin/companies', [
            'name' => 'Acme SpA',
            'email' => 'contacto@acme.cl',
        ]);

        $storeResponse
            ->assertCreated()
            ->assertJsonPath('data.name', 'Acme SpA');

        $companyId = $storeResponse->json('data.id');

        $this->assertDatabaseHas('companies', [
            'id' => $companyId,
            'name' => 'Acme SpA',
        ]);

        $deleteResponse = $this->deleteJson('/api/admin/companies/'.$companyId);

        $deleteResponse->assertNoContent();

        $this->assertSoftDeleted('companies', [
            'id' => $companyId,
        ]);
    }
}
