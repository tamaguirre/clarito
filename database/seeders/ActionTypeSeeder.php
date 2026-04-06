<?php

namespace Database\Seeders;

use App\Models\ActionType;
use Illuminate\Database\Seeder;

class ActionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect(['lectura simple', 'confirmacion', 'firma'])->each(function (string $name): void {
            ActionType::query()->firstOrCreate(['name' => $name]);
        });
    }
}
