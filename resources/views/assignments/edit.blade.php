@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Assignment</h1>

    <form action="{{ route('assignments.update', $assignment->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="course_id">Course</label>
            <select class="form-control" name="course_id" required>
               
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" @if($course->id == $assignment->course_id) selected @endif>{{ $course->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" name="title" value="{{ old('title', $assignment->title) }}" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" name="description">{{ old('description', $assignment->description) }}</textarea>
        </div>

        <div class="form-group">
            <label for="due_date">Due Date</label>
            <input type="date" class="form-control" name="due_date" value="{{ old('due_date', $assignment->due_date->toDateString()) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Assignment</button>
    </form>
</div>
@endsection
