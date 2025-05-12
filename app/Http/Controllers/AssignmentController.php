<?php

namespace App\Http\Controllers;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        return response()->json(Assignment::all());
    }

    public function create()
    {
         $courses = Course::all();
        return view('assignments.create',compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
        ]);

        $assignment = Assignment::create($request->all());

        return response()->json(['message' => 'Assignment created', 'assignment' => $assignment], 201);
    }

    public function show($id)
    {
        return response()->json(Assignment::findOrFail($id));
    }

    public function edit($id)
    {
        $assignment = Assignment::findOrFail($id);
        return view('assignments.edit', compact('assignment'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
        ]);

        $assignment = Assignment::findOrFail($id);
        $assignment->update($request->all());

        return response()->json(['message' => 'Assignment updated', 'assignment' => $assignment]);
    }

    public function destroy($id)
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->delete();

        return response()->json(['message' => 'Assignment deleted']);
    }
}
