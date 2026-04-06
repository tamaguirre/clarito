<?php

namespace Tests\Feature\Catalog;

use App\Models\Condition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConditionCatalogTest extends TestCase
{
    use RefreshDatabase;

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
