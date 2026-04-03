<?php

namespace Tests\Feature\Auth;

use App\Models\Condition;
use App\Models\EducationLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class RegisterApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(ClientRepository::class)->createPersonalAccessGrantClient('Test Personal Access Client');
    }

    public function test_user_can_register_with_valid_data(): void
    {
        $educationLevel = EducationLevel::factory()->create();
        $conditions = Condition::factory()->count(2)->create();

        $payload = [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'birth_date' => '1995-01-15',
            'education_level_id' => $educationLevel->id,
            'conditions' => $conditions->pluck('id')->all(),
        ];

        $response = $this->postJson('/api/register', $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('data.email', $payload['email'])
            ->assertJsonPath('data.birth_date', '1995-01-15')
            ->assertJsonPath('data.education_level.id', $educationLevel->id)
            ->assertJsonPath('meta.token_type', 'Bearer')
            ->assertJsonCount(2, 'data.conditions');

        $this->assertNotEmpty($response->json('meta.access_token'));

        $userId = $response->json('data.id');

        $this->assertDatabaseHas('users', [
            'email' => $payload['email'],
            'education_level_id' => $educationLevel->id,
        ]);

        $user = User::findOrFail($userId);
        $this->assertSame('1995-01-15', $user->birth_date?->toDateString());

        foreach ($conditions as $condition) {
            $this->assertDatabaseHas('condition_user', [
                'user_id' => $userId,
                'condition_id' => $condition->id,
            ]);
        }
    }

    public function test_register_validates_required_fields(): void
    {
        $response = $this->postJson('/api/register', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password', 'birth_date', 'education_level_id']);
    }
}
