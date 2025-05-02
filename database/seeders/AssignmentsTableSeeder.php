<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class AssignmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 10; $i++) {
            DB::table('assignments')->insert([
                'course_id' => rand(1, 10),
                'title' => 'Assignment ' . $i,
                'description' => 'Description for assignment ' . $i,
                'due_date' => now()->addDays(7),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
