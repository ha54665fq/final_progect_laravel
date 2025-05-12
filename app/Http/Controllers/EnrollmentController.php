<?php

namespace App\Http\Controllers;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index()
    {
        return response()->json(Enrollment::all());
    }

    public function create()
    {
   $students = \App\Models\User::where('role', 'student')->get();
    $courses = \App\Models\Course::all();

    return view('enrollments.create', compact('students', 'courses'));

    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $enrollment = Enrollment::create($request->all());

        return response()->json(['message' => 'Enrollment created', 'enrollment' => $enrollment], 201);
    }

    public function show($id)
    {
        return response()->json(Enrollment::findOrFail($id));
    }

    public function edit($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        return view('enrollments.edit', compact('enrollment'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $enrollment = Enrollment::findOrFail($id);
        $enrollment->update($request->all());

        return response()->json(['message' => 'Enrollment updated', 'enrollment' => $enrollment]);
    }

    public function destroy($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->delete();

        return response()->json(['message' => 'Enrollment deleted']);
    }
}
