<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use App\Http\Controllers\CourseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\HomeController;

// Authentication Routes
Auth::routes();

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Test route for debugging
Route::get('/test-gates', function () {
    if (!auth()->check()) {
        return 'Not authenticated';
    }

    $user = auth()->user();
    return [
        'user_id' => $user->id,
        'user_name' => $user->name,
        'user_role' => $user->role,
        'can_manage_courses' => Gate::allows('manage-courses'),
        'can_manage_assignments' => Gate::allows('manage-assignments'),
        'can_manage_enrollments' => Gate::allows('manage-enrollments'),
        'can_manage_submissions' => Gate::allows('manage-submissions'),
    ];
})->middleware('auth');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Routes accessible by all authenticated users
    Route::get('courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('courses/{course}', [CourseController::class, 'show'])->name('courses.show');

    Route::get('assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');

    Route::get('submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');

    // Simplified routes for testing - remove middleware temporarily
    Route::get('courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');

    Route::get('assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
    Route::post('assignments', [AssignmentController::class, 'store'])->name('assignments.store');
    Route::get('assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('assignments.edit');
    Route::put('assignments/{assignment}', [AssignmentController::class, 'update'])->name('assignments.update');
    Route::delete('assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');

    // Student-specific routes
    Route::get('submissions/create', [SubmissionController::class, 'create'])->name('submissions.create');
    Route::post('submissions', [SubmissionController::class, 'store'])->name('submissions.store');

    // Teacher and Admin routes for enrollments
    Route::resource('enrollments', EnrollmentController::class);

    // Teacher and Admin routes for submissions management
    Route::get('submissions/{submission}/edit', [SubmissionController::class, 'edit'])->name('submissions.edit');
    Route::put('submissions/{submission}', [SubmissionController::class, 'update'])->name('submissions.update');
    Route::delete('submissions/{submission}', [SubmissionController::class, 'destroy'])->name('submissions.destroy');

    // Admin-only routes
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class);
    });
});
