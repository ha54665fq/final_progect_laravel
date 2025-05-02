<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            DB::table('users')->insert([
                'name' => 'Student ' . $i,
                'email' => 'student' . $i . '@example.com',
                'password' => bcrypt('password'),
                'role' => 'student',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
