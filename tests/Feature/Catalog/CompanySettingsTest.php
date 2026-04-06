<?php

namespace Tests\Feature\Catalog;

use App\Models\AccessMethod;
use App\Models\ActionType;
use App\Models\AiTone;
use App\Models\Company;
use App\Models\Environment;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AccessMethodSeeder;
use Database\Seeders\ActionTypeSeeder;
use Database\Seeders\AiToneSeeder;
use Database\Seeders\CompanyConfigTypeSeeder;
use Database\Seeders\EnvironmentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CompanySettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_company_user_cannot_access_company_settings_endpoints(): void
    {
        $role = Role::query()->create(['name' => 'user']);

        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        Passport::actingAs($user, [], 'api');

        $response = $this->getJson('/api/company/catalogs');

        $response->assertForbidden();
    }

    public function test_company_user_can_read_and_update_settings_by_environment(): void
    {
        $this->seed([
            EnvironmentSeeder::class,
            ActionTypeSeeder::class,
            AccessMethodSeeder::class,
            AiToneSeeder::class,
            CompanyConfigTypeSeeder::class,
        ]);

        $companyUser = $this->createCompanyUser();
        $environment = Environment::query()->where('name', 'sandbox')->firstOrFail();

        $actionTypeId = ActionType::query()->where('name', 'confirmacion')->value('id');
        $accessMethodId = AccessMethod::query()->where('name', 'pin')->value('id');
        $aiToneId = AiTone::query()->where('name', 'formal')->value('id');

        Passport::actingAs($companyUser, [], 'api');

        $updateResponse = $this->putJson('/api/company/configs/'.$environment->id, [
            'action_type_id' => $actionTypeId,
            'allow_multiple_confirmation' => true,
            'link_expiration_hours' => 48,
            'access_method_id' => $accessMethodId,
            'ai_tone_id' => $aiToneId,
            'return_button' => [
                'text' => 'Volver',
                'url' => 'https://example.com/volver',
            ],
            'allow_calendar_dates' => true,
            'send_summary_pdf_by_email' => false,
        ]);

        $updateResponse
            ->assertOk()
            ->assertJsonPath('data.saved', true)
            ->assertJsonPath('data.settings.allow_multiple_confirmation', true)
            ->assertJsonPath('data.settings.link_expiration_hours', 48);

        $configCount = DB::table('company_environment_configs')
            ->where('company_id', $companyUser->company_id)
            ->where('environment_id', $environment->id)
            ->count();

        $this->assertSame(8, $configCount);

        $showResponse = $this->getJson('/api/company/configs/'.$environment->id);

        $showResponse
            ->assertOk()
            ->assertJsonPath('data.environment.name', 'sandbox')
            ->assertJsonPath('data.settings.link_expiration_hours', 48)
            ->assertJsonPath('data.settings.return_button.text', 'Volver')
            ->assertJsonPath('data.settings.allow_multiple_confirmation', true);
    }

    public function test_allow_multiple_confirmation_is_forced_false_when_action_is_not_confirmation(): void
    {
        $this->seed([
            EnvironmentSeeder::class,
            ActionTypeSeeder::class,
            AccessMethodSeeder::class,
            AiToneSeeder::class,
            CompanyConfigTypeSeeder::class,
        ]);

        $companyUser = $this->createCompanyUser();
        $environment = Environment::query()->where('name', 'production')->firstOrFail();

        $actionTypeId = ActionType::query()->where('name', 'lectura simple')->value('id');
        $accessMethodId = AccessMethod::query()->where('name', 'rut')->value('id');
        $aiToneId = AiTone::query()->where('name', 'tecnico')->value('id');

        Passport::actingAs($companyUser, [], 'api');

        $this->putJson('/api/company/configs/'.$environment->id, [
            'action_type_id' => $actionTypeId,
            'allow_multiple_confirmation' => true,
            'link_expiration_hours' => 12,
            'access_method_id' => $accessMethodId,
            'ai_tone_id' => $aiToneId,
            'return_button' => [
                'text' => 'Regresar',
                'url' => 'https://example.com/regresar',
            ],
            'allow_calendar_dates' => false,
            'send_summary_pdf_by_email' => true,
        ])->assertOk();

        $showResponse = $this->getJson('/api/company/configs/'.$environment->id);

        $showResponse
            ->assertOk()
            ->assertJsonPath('data.settings.allow_multiple_confirmation', false);
    }

    public function test_company_user_can_create_list_and_delete_webhooks_per_environment(): void
    {
        $this->seed([
            EnvironmentSeeder::class,
            ActionTypeSeeder::class,
            AccessMethodSeeder::class,
            AiToneSeeder::class,
            CompanyConfigTypeSeeder::class,
        ]);

        $companyUser = $this->createCompanyUser();
        $environment = Environment::query()->where('name', 'sandbox')->firstOrFail();

        Passport::actingAs($companyUser, [], 'api');

        $storeResponse = $this->postJson('/api/company/webhooks/'.$environment->id, [
            'name' => 'Webhook Principal',
            'url' => 'https://example.com/webhooks/clarito',
            'secret' => 'my-secret',
            'events' => ['resume.created', 'resume.updated'],
            'is_active' => true,
        ]);

        $storeResponse
            ->assertCreated()
            ->assertJsonPath('data.name', 'Webhook Principal');

        $webhookId = $storeResponse->json('data.id');

        $this->assertDatabaseHas('company_webhooks', [
            'id' => $webhookId,
            'company_id' => $companyUser->company_id,
            'environment_id' => $environment->id,
            'name' => 'Webhook Principal',
        ]);

        $listResponse = $this->getJson('/api/company/webhooks/'.$environment->id);

        $listResponse
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $webhookId);

        $deleteResponse = $this->deleteJson('/api/company/webhooks/'.$environment->id.'/'.$webhookId);

        $deleteResponse->assertNoContent();

        $this->assertSoftDeleted('company_webhooks', [
            'id' => $webhookId,
        ]);
    }

    private function createCompanyUser(): User
    {
        $role = Role::query()->firstOrCreate(['name' => 'company']);

        $company = Company::query()->create([
            'name' => 'Empresa Demo',
            'email' => 'empresa@example.com',
        ]);

        return User::factory()->create([
            'role_id' => $role->id,
            'company_id' => $company->id,
        ]);
    }
}
