<?php

namespace Database\Seeders;

use App\Models\AccessMethod;
use Illuminate\Database\Seeder;

class AccessMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect(['pin', 'clave unica', 'rut'])->each(function (string $name): void {
            AccessMethod::query()->firstOrCreate(['name' => $name]);
        });
    }
}
