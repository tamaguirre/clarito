<?php

namespace Database\Seeders;

use App\Models\CompanyType;
use Illuminate\Database\Seeder;

class CompanyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect(['Banca', 'Retail', 'RRHH', 'Legal'])->each(function (string $name): void {
            CompanyType::query()->firstOrCreate(['name' => $name]);
        });
    }
}
