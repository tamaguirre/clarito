<?php

namespace Tests\Feature\Catalog;

use App\Mail\CompanyRegistrationInvitationMail;
use App\Models\Company;
use App\Models\CompanyInvitation;
use App\Models\CompanyType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use Tests\TestCase;

class CompanyRegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_company_creation_sends_invitation_email(): void
    {
        Mail::fake();

        $adminRole = Role::query()->create(['name' => 'admin']);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        Passport::actingAs($admin, [], 'api');

        $response = $this->postJson('/api/admin/companies', [
            'name' => 'Nova Legal',
            'email' => 'onboarding@novalegal.cl',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.name', 'Nova Legal')
            ->assertJsonPath('meta.invitation_sent', true);

        $company = Company::query()->where('name', 'Nova Legal')->firstOrFail();

        $this->assertDatabaseHas('company_invitations', [
            'company_id' => $company->id,
        ]);

        Mail::assertSent(CompanyRegistrationInvitationMail::class);
    }

    public function test_invited_company_can_complete_registration_with_dictionary(): void
    {
        Storage::fake('public');

        $companyType = CompanyType::query()->create(['name' => 'Legal']);

        $company = Company::query()->create([
            'name' => 'Lex Team',
            'email' => 'contacto@lexteam.cl',
        ]);

        $invitation = CompanyInvitation::query()->create([
            'company_id' => $company->id,
            'token' => 'token-test-123',
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->post('/api/company-registrations/'.$invitation->token.'/complete', [
            'company_type_id' => $companyType->id,
            'phone' => '+56 9 1111 2222',
            'short_description' => 'Asesoria legal corporativa',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'logo' => UploadedFile::fake()->image('logo.png'),
            'dictionary' => [
                ['word' => 'SLA', 'definition' => 'Acuerdo de nivel de servicio'],
                ['word' => 'KPI', 'definition' => 'Indicador clave de rendimiento'],
            ],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.name', 'Lex Team')
            ->assertJsonPath('data.company_type.name', 'Legal')
            ->assertJsonCount(2, 'data.dictionary');

        $company->refresh();

        $this->assertNotNull($company->registration_completed_at);
        $this->assertNotNull($company->logo_path);
        $this->assertSame('+56 9 1111 2222', $company->phone);

        Storage::disk('public')->assertExists($company->logo_path);

        $this->assertDatabaseHas('company_dictionaries', [
            'company_id' => $company->id,
            'word' => 'SLA',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'contacto@lexteam.cl',
            'company_id' => $company->id,
        ]);

        $this->assertDatabaseHas('company_invitations', [
            'id' => $invitation->id,
        ]);

        $this->assertNotNull($invitation->fresh()->used_at);
    }
}
