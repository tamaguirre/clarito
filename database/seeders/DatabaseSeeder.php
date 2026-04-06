<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CompanyTypeSeeder::class,
            EducationLevelSeeder::class,
            ConditionSeeder::class,
        ]);

        // User::factory(10)->create();

        $userRole = Role::query()->where('name', 'user')->first();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role_id' => $userRole?->id,
        ]);
    }
}
