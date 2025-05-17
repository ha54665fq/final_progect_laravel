<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        $data = [];

        // Data for Admin
        if ($user->role === 'admin') {
            $data = [
                'users_count' => User::count(),
                'courses_count' => Course::count(),
                'assignments_count' => Assignment::count(),
                'submissions_count' => Submission::count(),
                'recent_users' => User::latest()->take(5)->get(),
                'recent_courses' => Course::latest()->take(5)->get(),
                'can_manage_users' => true,
                'can_manage_courses' => true,
                'can_manage_assignments' => true,
                'can_manage_enrollments' => true
            ];
        }
        // Data for Teacher
        elseif ($user->role === 'teacher') {
            $courses = Course::where('teacher_id', $user->id)->pluck('id');
            $data = [
                'my_courses' => Course::where('teacher_id', $user->id)->get(),
                'my_assignments' => Assignment::whereIn('course_id', $courses)->get(),
                'recent_submissions' => Submission::whereIn('assignment_id',
                    Assignment::whereIn('course_id', $courses)->pluck('id')
                )->latest()->take(5)->get(),
                'can_manage_courses' => true,
                'can_manage_assignments' => true,
                'can_manage_enrollments' => true
            ];
        }
        // Data for Student
        else {
            $enrolled_courses = $user->enrolledCourses()->pluck('courses.id');
            $data = [
                'my_courses' => $user->enrolledCourses,
                'my_assignments' => Assignment::whereIn('course_id', $enrolled_courses)->get(),
                'my_submissions' => $user->submissions()->with('assignment')->latest()->take(5)->get(),
                'can_submit_assignments' => true
            ];
        }

        return view('home', $data);
    }
}
