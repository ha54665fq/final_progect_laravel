<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EnrollmentController extends Controller
{
    /**
     * Get all enrollments
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'teacher') {
            // Teachers see enrollments for their courses
            $enrollments = Enrollment::whereHas('course', function($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })->with(['course', 'student'])->get();
        } else {
            // Students see only their own enrollments
            $enrollments = Enrollment::where('student_id', $user->id)
                ->with(['course', 'student'])->get();
        }

        return response()->json([
            'success' => true,
            'data' => $enrollments
        ]);
    }

    /**
     * Get enrollments for a specific course
     */
    public function courseEnrollments(Request $request, $courseId)
    {
        $user = $request->user();
        $course = Course::find($courseId);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found'
            ], 404);
        }

        // Check if user has access to this course
        if ($user->role === 'student') {
            // Students can only see their own enrollment in this course
            $enrollments = Enrollment::where('course_id', $courseId)
                ->where('student_id', $user->id)
                ->with(['course', 'student'])->get();
        } else {
            // Teachers can see all enrollments for their courses
            if ($course->teacher_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You can only view enrollments for your own courses.'
                ], 403);
            }

            $enrollments = Enrollment::where('course_id', $courseId)
                ->with(['course', 'student'])->get();
        }

        return response()->json([
            'success' => true,
            'data' => $enrollments
        ]);
    }

    /**
     * Get a specific enrollment
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $enrollment = Enrollment::with(['course', 'student'])->find($id);

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Enrollment not found'
            ], 404);
        }

        // Check if user has access to this enrollment
        if ($user->role === 'student' && $enrollment->student_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only view your own enrollments.'
            ], 403);
        }

        if ($user->role === 'teacher' && $enrollment->course->teacher_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only view enrollments for your own courses.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $enrollment
        ]);
    }

    /**
     * Enroll in a course (students only)
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'student') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only students can enroll in courses.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if student is already enrolled in this course
        $existingEnrollment = Enrollment::where('course_id', $request->course_id)
            ->where('student_id', $user->id)
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'success' => false,
                'message' => 'You are already enrolled in this course.'
            ], 400);
        }

        $enrollment = Enrollment::create([
            'course_id' => $request->course_id,
            'student_id' => $user->id,
            'enrolled_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully enrolled in course',
            'data' => $enrollment
        ], 201);
    }

    /**
     * Update an enrollment (teachers can update status)
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $enrollment = Enrollment::with(['course'])->find($id);

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Enrollment not found'
            ], 404);
        }

        if ($user->role === 'teacher') {
            // Teachers can only update enrollments for their courses
            if ($enrollment->course->teacher_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You can only update enrollments for your own courses.'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:active,inactive,completed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            $enrollment->update([
                'status' => $request->status,
            ]);
        } else {
            // Students cannot update enrollments
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Students cannot update enrollments.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Enrollment updated successfully',
            'data' => $enrollment
        ]);
    }

    /**
     * Drop a course (students can drop their own enrollments)
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $enrollment = Enrollment::find($id);

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Enrollment not found'
            ], 404);
        }

        if ($user->role !== 'student' || $enrollment->student_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only drop your own enrollments.'
            ], 403);
        }

        $enrollment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully dropped from course'
        ]);
    }
}
