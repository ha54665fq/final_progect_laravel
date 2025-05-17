<?php

namespace App\Http\Controllers;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Only teachers and admins can manage enrollments
        $this->middleware('can:manage-enrollments')->except(['index', 'show']);
    }

    public function index()
    {
        $enrollments = Enrollment::with(['student', 'course'])->get();
        return view('enrollments.index', compact('enrollments'));
    }

    public function create()
    {
        $students = User::where('role', 'student')->get();
        $courses = Course::all();
        return view('enrollments.create', compact('students', 'courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        // Check if enrollment already exists
        $exists = Enrollment::where('student_id', $request->student_id)
                          ->where('course_id', $request->course_id)
                          ->exists();

        if ($exists) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Student is already enrolled in this course.');
        }

        $enrollment = Enrollment::create($request->only(['student_id', 'course_id']));

        return redirect()->route('enrollments.index')
                        ->with('success', 'Enrollment created successfully.');
    }

    public function show($id)
    {
        $enrollment = Enrollment::with(['student', 'course'])->findOrFail($id);
        return view('enrollments.show', compact('enrollment'));
    }

    public function edit($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $students = User::where('role', 'student')->get();
        $courses = Course::all();

        return view('enrollments.edit', compact('enrollment', 'students', 'courses'));
    }

    public function update(Request $request, $id)
    {
        $enrollment = Enrollment::findOrFail($id);

        $request->validate([
            'student_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        // Check if the new enrollment combination already exists (excluding current enrollment)
        $exists = Enrollment::where('student_id', $request->student_id)
                          ->where('course_id', $request->course_id)
                          ->where('id', '!=', $id)
                          ->exists();

        if ($exists) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Student is already enrolled in this course.');
        }

        $enrollment->update($request->only(['student_id', 'course_id']));

        return redirect()->route('enrollments.index')
                        ->with('success', 'Enrollment updated successfully.');
    }

    public function destroy($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->delete();

        return redirect()->route('enrollments.index')
                        ->with('success', 'Enrollment deleted successfully.');
    }
}
