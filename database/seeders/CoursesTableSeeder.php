<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
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
        // Get all teachers
        $teachers = User::where('role', 'teacher')->get();

        if ($teachers->isEmpty()) {
            // Create a teacher if none exists
            $teacher = User::create([
                'name' => 'Teacher 1',
                'email' => 'teacher1@example.com',
                'password' => bcrypt('password'),
                'role' => 'teacher'
            ]);
            $teachers = collect([$teacher]);
        }

        // Create 5 courses
        foreach(range(1, 5) as $index) {
            Course::create([
                'title' => "Course $index",
                'description' => "Description for course $index",
                'teacher_id' => $teachers->random()->id
            ]);
        }
    }
}
