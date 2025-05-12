<?php

namespace App\Http\Controllers;
use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index()
    {
        return response()->json(Submission::all());
    }

    public function create()
    {
          $assignments = \App\Models\Assignment::all();
    $students = \App\Models\User::where('role', 'student')->get();

    return view('submissions.create', compact('assignments', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:assignments,id',
            'student_id' => 'required|exists:users,id',
            'file_path' => 'required|string',
            'submitted_at' => 'nullable|date',
            'grade' => 'nullable|numeric',
        ]);

        $submission = Submission::create($request->all());

        return response()->json(['message' => 'Submission created', 'submission' => $submission], 201);
    }

    public function show($id)
    {
        return response()->json(Submission::findOrFail($id));
    }

    public function edit($id)
    {
        $submission = Submission::findOrFail($id);
        return view('submissions.edit', compact('submission'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'file_path' => 'required|string',
            'submitted_at' => 'nullable|date',
            'grade' => 'nullable|numeric',
        ]);

        $submission = Submission::findOrFail($id);
        $submission->update($request->all());

        return response()->json(['message' => 'Submission updated', 'submission' => $submission]);
    }

    public function destroy($id)
    {
        $submission = Submission::findOrFail($id);
        $submission->delete();

        return response()->json(['message' => 'Submission deleted']);
    }
}
