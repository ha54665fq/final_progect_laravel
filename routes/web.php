<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Routes accessible by all authenticated users
    Route::get('courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('courses/{course}', [CourseController::class, 'show'])->name('courses.show');

    Route::get('assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');

    Route::get('submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');

    // Student-specific routes
    Route::middleware(['can:submit-assignments'])->group(function () {
        Route::get('submissions/create', [SubmissionController::class, 'create'])->name('submissions.create');
        Route::post('submissions', [SubmissionController::class, 'store'])->name('submissions.store');
    });

    // Teacher and Admin routes
    Route::middleware(['can:manage-courses'])->group(function () {
        Route::resource('courses', CourseController::class)->except(['index', 'show']);
    });

    Route::middleware(['can:manage-assignments'])->group(function () {
        Route::resource('assignments', AssignmentController::class)->except(['index', 'show']);
    });

    Route::middleware(['can:manage-enrollments'])->group(function () {
        Route::resource('enrollments', EnrollmentController::class);
    });

    Route::middleware(['can:manage-submissions'])->group(function () {
        Route::resource('submissions', SubmissionController::class)->only(['edit', 'update', 'destroy']);
    });

    // Admin-only routes
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class);
    });
});
