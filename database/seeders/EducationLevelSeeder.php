<?php

namespace Database\Seeders;

use App\Models\EducationLevel;
use Illuminate\Database\Seeder;

class EducationLevelSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $levels = [
            'Basico',
            'Medio',
            'Tecnico',
            'Universitario',
            'Postgrado',
        ];

        foreach ($levels as $name) {
            EducationLevel::query()->firstOrCreate(['name' => $name]);
        }
    }
}
