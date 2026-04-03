<?php

namespace Database\Seeders;

use App\Models\Condition;
use Illuminate\Database\Seeder;

class ConditionSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $conditions = [
            'Dislexia',
            'TDAH',
            'Discapacidad visual parcial',
            'Ninguna',
        ];

        foreach ($conditions as $name) {
            Condition::query()->firstOrCreate(['name' => $name]);
        }
    }
}
