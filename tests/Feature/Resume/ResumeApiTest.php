<?php

namespace Tests\Feature\Resume;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ResumeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_upload_pdf_resume(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Passport::actingAs($user, [], 'api');

        $file = UploadedFile::fake()->create('contrato.pdf', 256, 'application/pdf');

        $response = $this->postJson('/api/resumes', [
            'file' => $file,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.original_name', 'contrato.pdf')
            ->assertJsonPath('data.mime_type', 'application/pdf')
            ->assertJsonPath('data.summary_text.resume.0.section', 'Identificación de las Partes')
            ->assertJsonPath('data.tags.0', 'Arriendo');

        $this->assertNotEmpty($response->json('data.token'));

        $path = $response->json('data.file_path');

        Storage::disk('public')->assertExists($path);

        $this->assertDatabaseHas('resumes', [
            'user_id' => $user->id,
            'original_name' => 'contrato.pdf',
        ]);
    }

    public function test_authenticated_user_can_upload_image_resume(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Passport::actingAs($user, [], 'api');

        $file = UploadedFile::fake()->image('imagen.png');

        $response = $this->postJson('/api/resumes', [
            'file' => $file,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.user_id', $user->id);
    }

    public function test_authenticated_user_can_list_own_resumes(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Passport::actingAs($user, [], 'api');

        $first = UploadedFile::fake()->create('uno.pdf', 128, 'application/pdf');
        $second = UploadedFile::fake()->image('dos.png');

        $this->postJson('/api/resumes', ['file' => $first])->assertCreated();
        $this->postJson('/api/resumes', ['file' => $second])->assertCreated();

        $response = $this->getJson('/api/resumes');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.user_id', $user->id)
            ->assertJsonPath('data.0.tags.0', 'Arriendo');
    }

    public function test_resume_upload_requires_authentication(): void
    {
        $file = UploadedFile::fake()->create('contrato.pdf', 256, 'application/pdf');

        $response = $this->postJson('/api/resumes', [
            'file' => $file,
        ]);

        $response->assertUnauthorized();
    }

    public function test_resume_upload_rejects_invalid_file_type(): void
    {
        Passport::actingAs(User::factory()->create(), [], 'api');

        $file = UploadedFile::fake()->create('script.exe', 50, 'application/octet-stream');

        $response = $this->postJson('/api/resumes', [
            'file' => $file,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['file']);
    }

    public function test_authenticated_user_can_get_own_resume_by_token(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Passport::actingAs($user, [], 'api');

        $file = UploadedFile::fake()->create('documento.pdf', 256, 'application/pdf');

        $uploadResponse = $this->postJson('/api/resumes', [
            'file' => $file,
        ]);

        $token = $uploadResponse->json('data.token');

        $response = $this->getJson('/api/resumes/'.$token);

        $response
            ->assertOk()
            ->assertJsonPath('data.token', $token)
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.original_name', 'documento.pdf')
            ->assertJsonPath('data.summary_text.resume.1.section', 'Renta y Reajustabilidad');
    }

    public function test_user_cannot_get_other_user_resume(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create();
        Passport::actingAs($owner, [], 'api');

        $file = UploadedFile::fake()->create('privado.pdf', 256, 'application/pdf');

        $uploadResponse = $this->postJson('/api/resumes', [
            'file' => $file,
        ]);

        $token = $uploadResponse->json('data.token');

        Passport::actingAs(User::factory()->create(), [], 'api');

        $response = $this->getJson('/api/resumes/'.$token);

        $response->assertNotFound();
    }

    public function test_authenticated_user_can_update_resume_summary(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Passport::actingAs($user, [], 'api');

        $file = UploadedFile::fake()->create('contrato.pdf', 256, 'application/pdf');

        $uploadResponse = $this->postJson('/api/resumes', [
            'file' => $file,
        ]);

        $token = $uploadResponse->json('data.token');

        $summaryPayload = [
            'summary_text' => [
                'title' => 'Ignorado por fixture',
            ],
        ];

        $response = $this->patchJson('/api/resumes/'.$token, $summaryPayload);

        $response
            ->assertOk()
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.token', $token)
            ->assertJsonPath('data.summary_text.resume.4.section', 'Uso y Restricciones')
            ->assertJsonPath('data.summary_text.resume.0.faq.0.question', '¿Es necesario que ambos firmen ante notario?')
            ->assertJsonPath('data.original_name', 'contrato.pdf');
    }

    public function test_user_cannot_update_other_user_resume_summary(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create();
        Passport::actingAs($owner, [], 'api');

        $file = UploadedFile::fake()->create('privado.pdf', 256, 'application/pdf');

        $uploadResponse = $this->postJson('/api/resumes', [
            'file' => $file,
        ]);

        $token = $uploadResponse->json('data.token');

        Passport::actingAs(User::factory()->create(), [], 'api');

        $response = $this->patchJson('/api/resumes/'.$token, [
            'summary_text' => ['text' => 'Intento de hack'],
        ]);

        $response->assertNotFound();
    }
}
