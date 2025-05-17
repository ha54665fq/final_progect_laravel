<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\Assignment;
use App\Models\Enrollment;
use App\Models\Submission;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        // Admin sees all courses
        if ($user->role === 'admin') {
            $courses = Course::with(['teacher'])->get();
        }
        // Teacher sees their courses
        elseif ($user->role === 'teacher') {
            $courses = Course::where('teacher_id', $user->id)->with(['teacher'])->get();
        }
        // Student sees enrolled courses
        else {
            $courses = $user->enrolledCourses()->with(['teacher'])->get();
        }

        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        $this->authorize('manage-courses');
        $teachers = User::where('role', 'teacher')->get();
        return view('courses.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage-courses');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'required|exists:users,id'
        ]);

        Course::create($validated);

        return redirect()->route('courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function show(Course $course)
    {
        $course->load(['teacher', 'assignments', 'enrollments.student']);
        return view('courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $this->authorize('manage-courses');
        $teachers = User::where('role', 'teacher')->get();
        return view('courses.edit', compact('course', 'teachers'));
    }

    public function update(Request $request, Course $course)
    {
        $this->authorize('manage-courses');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'required|exists:users,id'
        ]);

        $course->update($validated);

        return redirect()->route('courses.index')
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $this->authorize('manage-courses');

        $course->delete();

        return redirect()->route('courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
