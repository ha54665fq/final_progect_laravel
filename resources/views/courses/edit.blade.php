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

        <button type="submit" class="btn btn-primary">Update Course</button>
    </form>
</div>
@endsection
