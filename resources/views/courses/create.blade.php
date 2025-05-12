@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create New Course</h1>

    <form action="{{ route('courses.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" name="description">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Create Course</button>
    </form>
</div>
@endsection
