<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssignmentController extends Controller
{
    /**
     * Get all assignments
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'teacher') {
            // Teachers see assignments for their courses
            $assignments = Assignment::whereHas('course', function($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })->with('course')->get();
        } else {
            // Students see all assignments
            $assignments = Assignment::with('course')->get();
        }

        return response()->json([
            'success' => true,
            'data' => $assignments
        ]);
    }

    /**
     * Get assignments for a specific course
     */
    public function courseAssignments(Request $request, $courseId)
    {
        $course = Course::find($courseId);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found'
            ], 404);
        }

        $assignments = Assignment::where('course_id', $courseId)->with('course')->get();

        return response()->json([
            'success' => true,
            'data' => $assignments
        ]);
    }

    /**
     * Get a specific assignment
     */
    public function show(Request $request, $id)
    {
        $assignment = Assignment::with('course', 'submissions')->find($id);

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $assignment
        ]);
    }

    /**
     * Create a new assignment (teachers only)
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'teacher') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only teachers can create assignments.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'course_id' => 'required|exists:courses,id',
            'due_date' => 'required|date|after:now',
            'max_score' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if the course belongs to the teacher
        $course = Course::find($request->course_id);
        if ($course->teacher_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only create assignments for your own courses.'
            ], 403);
        }

        $assignment = Assignment::create([
            'title' => $request->title,
            'description' => $request->description,
            'course_id' => $request->course_id,
            'due_date' => $request->due_date,
            'max_score' => $request->max_score,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Assignment created successfully',
            'data' => $assignment
        ], 201);
    }

    /**
     * Update an assignment (teachers only)
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $assignment = Assignment::with('course')->find($id);

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found'
            ], 404);
        }

        if ($user->role !== 'teacher' || $assignment->course->teacher_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only update assignments for your own courses.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'due_date' => 'sometimes|required|date',
            'max_score' => 'sometimes|required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $assignment->update($request->only(['title', 'description', 'due_date', 'max_score']));

        return response()->json([
            'success' => true,
            'message' => 'Assignment updated successfully',
            'data' => $assignment
        ]);
    }

    /**
     * Delete an assignment (teachers only)
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $assignment = Assignment::with('course')->find($id);

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found'
            ], 404);
        }

        if ($user->role !== 'teacher' || $assignment->course->teacher_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only delete assignments for your own courses.'
            ], 403);
        }

        $assignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Assignment deleted successfully'
        ]);
    }
}
