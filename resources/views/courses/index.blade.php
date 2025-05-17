@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between mb-4">
        <div class="col-auto">
            <h2>Courses</h2>
        </div>
        @if(auth()->user()->role !== 'student')
        <div class="col-auto">
            <a href="{{ route('courses.create') }}" class="btn btn-primary">Create New Course</a>
        </div>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        @forelse($courses as $course)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $course->title }}</h5>
                        <p class="card-text">
                            <strong>Teacher:</strong> {{ $course->teacher->name }}<br>
                            <strong>Students:</strong> {{ $course->enrollments->count() }}
                        </p>
                        <p class="card-text">{{ Str::limit($course->description, 100) }}</p>

                        <div class="btn-group">
                            <a href="{{ route('courses.show', $course) }}" class="btn btn-info btn-sm">View</a>

                            @if(auth()->user()->role !== 'student')
                                <a href="{{ route('courses.edit', $course) }}" class="btn btn-primary btn-sm">Edit</a>
                                <form action="{{ route('courses.destroy', $course) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course?')">Delete</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No courses available.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
