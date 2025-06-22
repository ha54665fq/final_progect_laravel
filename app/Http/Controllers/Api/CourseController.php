<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    /**
     * Get all courses
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'teacher') {
            // Teachers see only their courses
            $courses = Course::where('teacher_id', $user->id)->with('teacher')->get();
        } else {
            // Students see all courses
            $courses = Course::with('teacher')->get();
        }

        return response()->json([
            'success' => true,
            'data' => $courses
        ]);
    }

    /**
     * Get a specific course
     */
    public function show(Request $request, $id)
    {
        $course = Course::with('teacher', 'assignments')->find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $course
        ]);
    }

    /**
     * Create a new course (teachers only)
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'teacher') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only teachers can create courses.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'code' => 'required|string|max:50|unique:courses',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $course = Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'code' => $request->code,
            'teacher_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Course created successfully',
            'data' => $course
        ], 201);
    }

    /**
     * Update a course (teachers only)
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found'
            ], 404);
        }

        if ($user->role !== 'teacher' || $course->teacher_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only update your own courses.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'code' => 'sometimes|required|string|max:50|unique:courses,code,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $course->update($request->only(['title', 'description', 'code']));

        return response()->json([
            'success' => true,
            'message' => 'Course updated successfully',
            'data' => $course
        ]);
    }

    /**
     * Delete a course (teachers only)
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found'
            ], 404);
        }

        if ($user->role !== 'teacher' || $course->teacher_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only delete your own courses.'
            ], 403);
        }

        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course deleted successfully'
        ]);
    }
}
