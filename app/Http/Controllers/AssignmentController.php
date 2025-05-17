<?php

namespace App\Http\Controllers;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Only teachers and admins can manage assignments
        $this->middleware('can:manage-assignments')->except(['index', 'show']);
    }

    public function index()
    {
        if (auth()->user()->role === 'student') {
            // Students only see assignments from courses they're enrolled in
            $assignments = Assignment::whereHas('course.enrollments', function($query) {
                $query->where('student_id', auth()->id());
            })->with('course')->get();
        } else {
            $assignments = Assignment::with('course')->get();
        }
        return view('assignments.index', compact('assignments'));
    }

    public function create()
    {
        $courses = Course::all();
        return view('assignments.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date|after:today',
        ]);

        $assignment = Assignment::create($request->all());

        return redirect()->route('assignments.index')
                        ->with('success', 'Assignment created successfully.');
    }

    public function show($id)
    {
        $assignment = Assignment::with(['course', 'submissions.student'])->findOrFail($id);

        // Check if student has access to this assignment
        if (auth()->user()->role === 'student') {
            $hasAccess = $assignment->course->enrollments()
                ->where('student_id', auth()->id())
                ->exists();

            if (!$hasAccess) {
                return redirect()->route('assignments.index')
                    ->with('error', 'You do not have access to this assignment.');
            }
        }

        return view('assignments.show', compact('assignment'));
    }

    public function edit($id)
    {
        $assignment = Assignment::findOrFail($id);
        $courses = Course::all();
        return view('assignments.edit', compact('assignment', 'courses'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
        ]);

        $assignment = Assignment::findOrFail($id);
        $assignment->update($request->all());

        return redirect()->route('assignments.index')
                        ->with('success', 'Assignment updated successfully.');
    }

    public function destroy($id)
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->delete();

        return redirect()->route('assignments.index')
                        ->with('success', 'Assignment deleted successfully.');
    }
}
