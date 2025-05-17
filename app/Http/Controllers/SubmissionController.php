<?php

namespace App\Http\Controllers;
use App\Models\Submission;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Students can only view their own submissions
        // Teachers can view all submissions for their courses
        // Admins can view all submissions
    }

    public function index()
    {
        if (auth()->user()->role === 'student') {
            $submissions = Submission::where('student_id', auth()->id())->with(['assignment', 'student'])->get();
        } else {
            $submissions = Submission::with(['assignment', 'student'])->get();
        }
        return view('submissions.index', compact('submissions'));
    }

    public function create()
    {
        if (auth()->user()->role === 'student') {
            $assignments = Assignment::whereHas('course.enrollments', function($query) {
                $query->where('student_id', auth()->id());
            })->get();
            $students = collect([auth()->user()]);
        } else {
            $assignments = Assignment::all();
            $students = User::where('role', 'student')->get();
        }

        return view('submissions.create', compact('assignments', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:assignments,id',
            'student_id' => 'required|exists:users,id',
            'file_path' => 'required|string',
            'submitted_at' => 'nullable|date',
            'grade' => 'nullable|numeric|min:0|max:100',
        ]);

        // Check if user is authorized to submit
        if (auth()->user()->role === 'student' && auth()->id() != $request->student_id) {
            return redirect()->back()->with('error', 'You can only submit your own assignments.');
        }

        $submission = Submission::create($request->all());

        return redirect()->route('submissions.index')
                        ->with('success', 'Submission created successfully.');
    }

    public function show($id)
    {
        $submission = Submission::with(['assignment', 'student'])->findOrFail($id);

        // Check if user is authorized to view this submission
        if (auth()->user()->role === 'student' && auth()->id() != $submission->student_id) {
            return redirect()->route('submissions.index')->with('error', 'Unauthorized access.');
        }

        return view('submissions.show', compact('submission'));
    }

    public function edit($id)
    {
        $submission = Submission::findOrFail($id);

        // Only teachers and admins can edit submissions (for grading)
        if (auth()->user()->role === 'student') {
            return redirect()->route('submissions.index')->with('error', 'Students cannot edit submissions.');
        }

        return view('submissions.edit', compact('submission'));
    }

    public function update(Request $request, $id)
    {
        $submission = Submission::findOrFail($id);

        // Only teachers and admins can update submissions
        if (auth()->user()->role === 'student') {
            return redirect()->route('submissions.index')->with('error', 'Students cannot update submissions.');
        }

        $request->validate([
            'file_path' => 'required|string',
            'submitted_at' => 'nullable|date',
            'grade' => 'nullable|numeric|min:0|max:100',
        ]);

        $submission->update($request->all());

        return redirect()->route('submissions.index')
                        ->with('success', 'Submission updated successfully.');
    }

    public function destroy($id)
    {
        $submission = Submission::findOrFail($id);

        // Only admins can delete submissions
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('submissions.index')->with('error', 'Only administrators can delete submissions.');
        }

        $submission->delete();

        return redirect()->route('submissions.index')
                        ->with('success', 'Submission deleted successfully.');
    }
}
