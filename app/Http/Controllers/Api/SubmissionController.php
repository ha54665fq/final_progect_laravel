<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubmissionController extends Controller
{
    /**
     * Get all submissions
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'teacher') {
            // Teachers see submissions for assignments in their courses
            $submissions = Submission::whereHas('assignment.course', function($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })->with(['assignment', 'student'])->get();
        } else {
            // Students see only their own submissions
            $submissions = Submission::where('student_id', $user->id)
                ->with(['assignment', 'student'])->get();
        }

        return response()->json([
            'success' => true,
            'data' => $submissions
        ]);
    }

    /**
     * Get submissions for a specific assignment
     */
    public function assignmentSubmissions(Request $request, $assignmentId)
    {
        $user = $request->user();
        $assignment = Assignment::with('course')->find($assignmentId);

        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found'
            ], 404);
        }

        // Check if user has access to this assignment
        if ($user->role === 'student') {
            // Students can only see their own submissions
            $submissions = Submission::where('assignment_id', $assignmentId)
                ->where('student_id', $user->id)
                ->with(['assignment', 'student'])->get();
        } else {
            // Teachers can see all submissions for their assignments
            if ($assignment->course->teacher_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You can only view submissions for your own assignments.'
                ], 403);
            }

            $submissions = Submission::where('assignment_id', $assignmentId)
                ->with(['assignment', 'student'])->get();
        }

        return response()->json([
            'success' => true,
            'data' => $submissions
        ]);
    }

    /**
     * Get a specific submission
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $submission = Submission::with(['assignment', 'student'])->find($id);

        if (!$submission) {
            return response()->json([
                'success' => false,
                'message' => 'Submission not found'
            ], 404);
        }

        // Check if user has access to this submission
        if ($user->role === 'student' && $submission->student_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only view your own submissions.'
            ], 403);
        }

        if ($user->role === 'teacher' && $submission->assignment->course->teacher_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only view submissions for your own assignments.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $submission
        ]);
    }

    /**
     * Submit an assignment (students only)
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'student') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only students can submit assignments.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'assignment_id' => 'required|exists:assignments,id',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if student already submitted this assignment
        $existingSubmission = Submission::where('assignment_id', $request->assignment_id)
            ->where('student_id', $user->id)
            ->first();

        if ($existingSubmission) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted this assignment.'
            ], 400);
        }

        $submission = Submission::create([
            'assignment_id' => $request->assignment_id,
            'student_id' => $user->id,
            'content' => $request->content,
            'submitted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Assignment submitted successfully',
            'data' => $submission
        ], 201);
    }

    /**
     * Update a submission (students can update their own, teachers can grade)
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $submission = Submission::with(['assignment.course'])->find($id);

        if (!$submission) {
            return response()->json([
                'success' => false,
                'message' => 'Submission not found'
            ], 404);
        }

        if ($user->role === 'student') {
            // Students can only update their own submissions before grading
            if ($submission->student_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You can only update your own submissions.'
                ], 403);
            }

            if ($submission->grade !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot update submission after it has been graded.'
                ], 400);
            }

            $validator = Validator::make($request->all(), [
                'content' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $submission->update([
                'content' => $request->content,
                'submitted_at' => now(),
            ]);
        } else {
            // Teachers can only grade submissions for their assignments
            if ($submission->assignment->course->teacher_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You can only grade submissions for your own assignments.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'grade' => 'required|numeric|min:0|max:' . $submission->assignment->max_score,
                'feedback' => 'sometimes|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $submission->update([
                'grade' => $request->grade,
                'feedback' => $request->feedback,
                'graded_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Submission updated successfully',
            'data' => $submission
        ]);
    }

    /**
     * Delete a submission (students can delete their own ungraded submissions)
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $submission = Submission::find($id);

        if (!$submission) {
            return response()->json([
                'success' => false,
                'message' => 'Submission not found'
            ], 404);
        }

        if ($user->role !== 'student' || $submission->student_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only delete your own submissions.'
            ], 403);
        }

        if ($submission->grade !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete submission after it has been graded.'
            ], 400);
        }

        $submission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Submission deleted successfully'
        ]);
    }
}
