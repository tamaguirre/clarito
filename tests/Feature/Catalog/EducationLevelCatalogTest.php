<?php

namespace Tests\Feature\Catalog;

use App\Models\EducationLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EducationLevelCatalogTest extends TestCase
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
}
