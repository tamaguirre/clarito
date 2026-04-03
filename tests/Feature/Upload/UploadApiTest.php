<?php

namespace Tests\Feature\Upload;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UploadApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_upload_pdf(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Passport::actingAs($user, [], 'api');

        $file = UploadedFile::fake()->create('contrato.pdf', 256, 'application/pdf');

        $response = $this->postJson('/api/uploads', [
            'file' => $file,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.original_name', 'contrato.pdf')
            ->assertJsonPath('data.mime_type', 'application/pdf');

        $this->assertNotEmpty($response->json('data.token'));

        $path = $response->json('data.file_path');

        Storage::disk('public')->assertExists($path);

        $this->assertDatabaseHas('uploads', [
            'user_id' => $user->id,
            'original_name' => 'contrato.pdf',
        ]);
    }

    public function test_authenticated_user_can_upload_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Passport::actingAs($user, [], 'api');

        $file = UploadedFile::fake()->image('imagen.png');

        $response = $this->postJson('/api/uploads', [
            'file' => $file,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.user_id', $user->id);
    }

    public function test_upload_requires_authentication(): void
    {
        $file = UploadedFile::fake()->create('contrato.pdf', 256, 'application/pdf');

        $response = $this->postJson('/api/uploads', [
            'file' => $file,
        ]);

        $response->assertUnauthorized();
    }

    public function test_upload_rejects_invalid_file_type(): void
    {
        Passport::actingAs(User::factory()->create(), [], 'api');

        $file = UploadedFile::fake()->create('script.exe', 50, 'application/octet-stream');

        $response = $this->postJson('/api/uploads', [
            'file' => $file,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['file']);
    }

    public function test_authenticated_user_can_get_uploaded_document_by_token(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        Passport::actingAs($user, [], 'api');

        $file = UploadedFile::fake()->create('contrato.pdf', 256, 'application/pdf');

        $storeResponse = $this->postJson('/api/uploads', [
            'file' => $file,
        ]);

        $token = $storeResponse->json('data.token');

        $response = $this->getJson('/api/uploads/'.$token);

        $response
            ->assertOk()
            ->assertJsonPath('data.token', $token)
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.original_name', 'contrato.pdf');
    }

    public function test_user_cannot_get_other_user_uploaded_document_by_token(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create();
        Passport::actingAs($owner, [], 'api');

        $file = UploadedFile::fake()->create('privado.pdf', 256, 'application/pdf');

        $storeResponse = $this->postJson('/api/uploads', [
            'file' => $file,
        ]);

        $token = $storeResponse->json('data.token');

        Passport::actingAs(User::factory()->create(), [], 'api');

        $response = $this->getJson('/api/uploads/'.$token);

        $response->assertNotFound();
    }
}
