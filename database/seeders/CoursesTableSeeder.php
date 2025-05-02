<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CoursesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            DB::table('courses')->insert([
                'title' => 'Course ' . $i,
                'description' => 'Description for course ' . $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
