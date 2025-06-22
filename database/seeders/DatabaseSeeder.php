<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CreateAdminUserSeeder::class,
            UsersTableSeeder::class,
            CoursesTableSeeder::class,
            AssignmentsTableSeeder::class,
            EnrollmentsTableSeeder::class,
            SubmissionsTableSeeder::class,
            PersonalAccessTokensTableSeeder::class,
            PasswordResetsTableSeeder::class,
        ]);
    }
}
