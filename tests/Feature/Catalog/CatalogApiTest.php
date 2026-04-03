<?php

namespace Tests\Feature\Catalog;

use App\Models\Condition;
use App\Models\EducationLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_education_levels_sorted_by_name(): void
    {
        EducationLevel::create(['name' => 'Universitario']);
        EducationLevel::create(['name' => 'Basico']);
        EducationLevel::create(['name' => 'Tecnico']);

        $response = $this->getJson('/api/education-levels');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    ['id', 'name'],
                ],
            ])
            ->assertJsonPath('data.0.name', 'Basico')
            ->assertJsonPath('data.1.name', 'Tecnico')
            ->assertJsonPath('data.2.name', 'Universitario');
    }

    public function test_it_lists_conditions_sorted_by_name(): void
    {
        Condition::create(['name' => 'TDAH']);
        Condition::create(['name' => 'Dislexia']);
        Condition::create(['name' => 'Ninguna']);

        $response = $this->getJson('/api/conditions');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    ['id', 'name'],
                ],
            ])
            ->assertJsonPath('data.0.name', 'Dislexia')
            ->assertJsonPath('data.1.name', 'Ninguna')
            ->assertJsonPath('data.2.name', 'TDAH');
    }
}
