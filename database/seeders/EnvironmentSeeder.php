<?php

namespace Database\Seeders;

use App\Models\Environment;
use Illuminate\Database\Seeder;

class EnvironmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect(['sandbox', 'production'])->each(function (string $name): void {
            Environment::query()->firstOrCreate(['name' => $name]);
        });
    }
}
