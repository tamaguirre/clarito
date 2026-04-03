<?php

namespace Tests\Feature\Auth;

use App\Models\Condition;
use App\Models\EducationLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_valid_data(): void
    {
        $educationLevel = EducationLevel::create(['name' => 'Universitario']);
        $conditionA = Condition::create(['name' => 'TDAH']);
        $conditionB = Condition::create(['name' => 'Dislexia']);

        $response = $this->postJson('/api/register', [
            'name' => 'Tam Test',
            'email' => 'tam@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'birth_date' => '1995-01-15',
            'education_level_id' => $educationLevel->id,
            'conditions' => [$conditionA->id, $conditionB->id],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.email', 'tam@example.com')
            ->assertJsonPath('data.birth_date', '1995-01-15')
            ->assertJsonPath('data.education_level.id', $educationLevel->id)
            ->assertJsonCount(2, 'data.conditions');

        $userId = $response->json('data.id');

        $this->assertDatabaseHas('users', [
            'email' => 'tam@example.com',
            'education_level_id' => $educationLevel->id,
        ]);

        $user = User::findOrFail($userId);
        $this->assertSame('1995-01-15', $user->birth_date?->toDateString());

        $this->assertDatabaseHas('condition_user', [
            'user_id' => $userId,
            'condition_id' => $conditionA->id,
        ]);

        $this->assertDatabaseHas('condition_user', [
            'user_id' => $userId,
            'condition_id' => $conditionB->id,
        ]);
    }

    public function test_register_validates_required_fields(): void
    {
        $response = $this->postJson('/api/register', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password', 'birth_date', 'education_level_id', 'conditions']);
    }
}
