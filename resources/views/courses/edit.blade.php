@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Course</h1>

    <form action="{{ route('courses.update', $course->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" name="title" value="{{ old('title', $course->title) }}" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" name="description">{{ old('description', $course->description) }}</textarea>
        </div>

        <div class="form-group">
            <label for="teacher_id">Teacher</label>
            <select class="form-control" name="teacher_id" required>
                <option value="">Select Teacher</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ old('teacher_id', $course->teacher_id) == $teacher->id ? 'selected' : '' }}>
                        {{ $teacher->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Course</button>
    </form>
</div>
@endsection
