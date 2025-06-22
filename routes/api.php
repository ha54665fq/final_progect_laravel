<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\SubmissionController;
use App\Http\Controllers\Api\EnrollmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (no authentication required)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    // Course routes
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);
    Route::post('/courses', [CourseController::class, 'store']);
    Route::put('/courses/{id}', [CourseController::class, 'update']);
    Route::delete('/courses/{id}', [CourseController::class, 'destroy']);

    // Assignment routes
    Route::get('/assignments', [AssignmentController::class, 'index']);
    Route::get('/assignments/{id}', [AssignmentController::class, 'show']);
    Route::get('/courses/{courseId}/assignments', [AssignmentController::class, 'courseAssignments']);
    Route::post('/assignments', [AssignmentController::class, 'store']);
    Route::put('/assignments/{id}', [AssignmentController::class, 'update']);
    Route::delete('/assignments/{id}', [AssignmentController::class, 'destroy']);

    // Submission routes
    Route::get('/submissions', [SubmissionController::class, 'index']);
    Route::get('/submissions/{id}', [SubmissionController::class, 'show']);
    Route::get('/assignments/{assignmentId}/submissions', [SubmissionController::class, 'assignmentSubmissions']);
    Route::post('/submissions', [SubmissionController::class, 'store']);
    Route::put('/submissions/{id}', [SubmissionController::class, 'update']);
    Route::delete('/submissions/{id}', [SubmissionController::class, 'destroy']);

    // Enrollment routes
    Route::get('/enrollments', [EnrollmentController::class, 'index']);
    Route::get('/enrollments/{id}', [EnrollmentController::class, 'show']);
    Route::get('/courses/{courseId}/enrollments', [EnrollmentController::class, 'courseEnrollments']);
    Route::post('/enrollments', [EnrollmentController::class, 'store']);
    Route::put('/enrollments/{id}', [EnrollmentController::class, 'update']);
    Route::delete('/enrollments/{id}', [EnrollmentController::class, 'destroy']);
});

// Test route to check API status
Route::get('/status', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});
