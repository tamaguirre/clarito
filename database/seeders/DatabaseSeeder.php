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
            EnvironmentSeeder::class,
            ActionTypeSeeder::class,
            AccessMethodSeeder::class,
            AiToneSeeder::class,
            CompanyConfigTypeSeeder::class,
            EducationLevelSeeder::class,
            ConditionSeeder::class,
        ]);

        // User::factory(10)->create();

        $userRole = Role::query()->where('name', 'user')->first();

        $testUser = User::query()->withTrashed()->firstOrNew([
            'email' => 'test@example.com',
        ]);

        $testUser->name = 'Test User';
        $testUser->role_id = $userRole?->id;

        if (! $testUser->exists) {
            $testUser->password = 'password';
        }

        $testUser->save();

        if ($testUser->trashed()) {
            $testUser->restore();
        }
    }
}
