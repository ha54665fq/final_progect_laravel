@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create New Assignment</h1>

    <form action="{{ route('assignments.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="course_id">Course</label>
            <select class="form-control" name="course_id" required>
               
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" name="title" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" name="description"></textarea>
        </div>

        <div class="form-group">
            <label for="due_date">Due Date</label>
            <input type="date" class="form-control" name="due_date" required>
        </div>

        <button type="submit" class="btn btn-primary">Create Assignment</button>
    </form>
</div>
@endsection
