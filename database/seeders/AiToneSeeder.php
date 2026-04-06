<?php

namespace Database\Seeders;

use App\Models\AiTone;
use Illuminate\Database\Seeder;

class AiToneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect(['empatico', 'formal', 'tecnico'])->each(function (string $name): void {
            AiTone::query()->firstOrCreate(['name' => $name]);
        });
    }
}
