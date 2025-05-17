@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Admin Dashboard -->
                    @if(auth()->user()->role === 'admin')
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Users</h5>
                                        <p class="h3">{{ $users_count }}</p>
                                        <a href="{{ route('users.index') }}" class="btn btn-primary">Manage Users</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Courses</h5>
                                        <p class="h3">{{ $courses_count }}</p>
                                        <a href="{{ route('courses.index') }}" class="btn btn-primary">Manage Courses</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Assignments</h5>
                                        <p class="h3">{{ $assignments_count }}</p>
                                        <a href="{{ route('assignments.index') }}" class="btn btn-primary">Manage Assignments</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Submissions</h5>
                                        <p class="h3">{{ $submissions_count }}</p>
                                        <a href="{{ route('submissions.index') }}" class="btn btn-primary">View Submissions</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Teacher Dashboard -->
                    @if(auth()->user()->role === 'teacher')
                        <div class="row mb-4">
                            <div class="col-md-12 mb-4">
                                <h4>My Courses</h4>
                                <a href="{{ route('courses.create') }}" class="btn btn-success mb-3">Create New Course</a>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Students</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($my_courses as $course)
                                            <tr>
                                                <td>{{ $course->title }}</td>
                                                <td>{{ $course->enrollments->count() }}</td>
                                                <td>
                                                    <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-info">View</a>
                                                    <a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-primary">Edit</a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <h4>Recent Submissions</h4>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Student</th>
                                                <th>Assignment</th>
                                                <th>Submitted</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recent_submissions as $submission)
                                            <tr>
                                                <td>{{ $submission->student->name }}</td>
                                                <td>{{ $submission->assignment->title }}</td>
                                                <td>{{ $submission->created_at->diffForHumans() }}</td>
                                                <td>
                                                    <a href="{{ route('submissions.show', $submission) }}" class="btn btn-sm btn-info">View</a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Student Dashboard -->
                    @if(auth()->user()->role === 'student')
                        <div class="row mb-4">
                            <div class="col-md-12 mb-4">
                                <h4>My Courses</h4>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Course</th>
                                                <th>Teacher</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($my_courses as $course)
                                            <tr>
                                                <td>{{ $course->title }}</td>
                                                <td>{{ $course->teacher->name }}</td>
                                                <td>
                                                    <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-info">View</a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <h4>Recent Assignments</h4>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Course</th>
                                                <th>Due Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($my_assignments as $assignment)
                                            <tr>
                                                <td>{{ $assignment->title }}</td>
                                                <td>{{ $assignment->course->title }}</td>
                                                <td>{{ $assignment->due_date }}</td>
                                                <td>
                                                    <a href="{{ route('assignments.show', $assignment) }}" class="btn btn-sm btn-info">View</a>
                                                    @if(!$assignment->submissions->where('student_id', auth()->id())->count())
                                                        <a href="{{ route('submissions.create', ['assignment' => $assignment->id]) }}" class="btn btn-sm btn-success">Submit</a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
