<?php

namespace Tests\Feature\Resume;

use App\Models\Company;
use App\Models\Environment;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\EnvironmentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CompanyResumeUsageTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_resume_upload_tracks_company_environment_and_usage_log(): void
    {
        Storage::fake('public');
        $this->seed(EnvironmentSeeder::class);

        config(['app.production_mode' => false]);

        $user = $this->createCompanyUser();
        Passport::actingAs($user, [], 'api');

        $response = $this->postJson('/api/resumes', [
            'file' => UploadedFile::fake()->create('company-contract.pdf', 128, 'application/pdf'),
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.company_id', $user->company_id);

        $resumeId = $response->json('data.id');
        $sandboxEnvironmentId = Environment::query()->where('name', 'sandbox')->value('id');

        $this->assertDatabaseHas('resumes', [
            'id' => $resumeId,
            'company_id' => $user->company_id,
            'environment_id' => $sandboxEnvironmentId,
        ]);

        $this->assertDatabaseHas('company_resume_usages', [
            'company_id' => $user->company_id,
            'environment_id' => $sandboxEnvironmentId,
            'resume_id' => $resumeId,
            'user_id' => $user->id,
            'action' => 'resume.created',
        ]);
    }

    public function test_company_can_list_its_resume_usage_logs_filtered_by_environment(): void
    {
        Storage::fake('public');
        $this->seed(EnvironmentSeeder::class);

        $user = $this->createCompanyUser();
        Passport::actingAs($user, [], 'api');

        config(['app.production_mode' => false]);
        $this->postJson('/api/resumes', [
            'file' => UploadedFile::fake()->create('sandbox.pdf', 128, 'application/pdf'),
        ])->assertCreated();

        config(['app.production_mode' => true]);
        $this->postJson('/api/resumes', [
            'file' => UploadedFile::fake()->create('production.pdf', 128, 'application/pdf'),
        ])->assertCreated();

        $sandboxId = Environment::query()->where('name', 'sandbox')->value('id');
        $productionId = Environment::query()->where('name', 'production')->value('id');

        $allLogsResponse = $this->getJson('/api/company/resume-usages');

        $allLogsResponse
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $sandboxLogsResponse = $this->getJson('/api/company/resume-usages?environment_id='.$sandboxId);

        $sandboxLogsResponse
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.environment', 'sandbox');

        $productionLogsResponse = $this->getJson('/api/company/resume-usages?environment_id='.$productionId);

        $productionLogsResponse
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.environment', 'production');
    }

    public function test_non_company_user_cannot_access_company_resume_usage_logs(): void
    {
        $role = Role::query()->firstOrCreate(['name' => 'user']);
        $user = User::factory()->create(['role_id' => $role->id]);

        Passport::actingAs($user, [], 'api');

        $response = $this->getJson('/api/company/resume-usages');

        $response->assertForbidden();
    }

    private function createCompanyUser(): User
    {
        $role = Role::query()->firstOrCreate(['name' => 'company']);

        $company = Company::query()->create([
            'name' => 'Empresa Log Test',
            'email' => 'logs@example.com',
        ]);

        return User::factory()->create([
            'role_id' => $role->id,
            'company_id' => $company->id,
        ]);
    }
}
