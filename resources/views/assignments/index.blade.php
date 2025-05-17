@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between mb-4">
        <div class="col-auto">
            <h2>Assignments</h2>
        </div>
        @if(auth()->user()->role !== 'student')
        <div class="col-auto">
            <a href="{{ route('assignments.create') }}" class="btn btn-primary">Create New Assignment</a>
        </div>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        @forelse($assignments as $assignment)
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $assignment->title }}</h5>
                        <p class="card-text">
                            <strong>Course:</strong> {{ $assignment->course->title }}<br>
                            <strong>Due Date:</strong> {{ $assignment->due_date->format('Y-m-d') }}<br>
                            <strong>Submissions:</strong> {{ $assignment->submissions->count() }}
                        </p>
                        <p class="card-text">{{ Str::limit($assignment->description, 100) }}</p>

                        <div class="btn-group">
                            <a href="{{ route('assignments.show', $assignment) }}" class="btn btn-info btn-sm">View</a>

                            @if(auth()->user()->role !== 'student')
                                <a href="{{ route('assignments.edit', $assignment) }}" class="btn btn-primary btn-sm">Edit</a>
                                <form action="{{ route('assignments.destroy', $assignment) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this assignment?')">Delete</button>
                                </form>
                            @else
                                @if(!$assignment->submissions->where('student_id', auth()->id())->count())
                                    <a href="{{ route('submissions.create', ['assignment' => $assignment->id]) }}" class="btn btn-success btn-sm">Submit</a>
                                @else
                                    <button class="btn btn-secondary btn-sm" disabled>Submitted</button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No assignments available.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
