@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Enrollment</h1>

    <form action="{{ route('enrollments.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="student_id">Student</label>
            <select class="form-control" name="student_id" required>
            
                @foreach ($students as $student)
                    <option value="{{ $student->id }}">{{ $student->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="course_id">Course</label>
            <select class="form-control" name="course_id" required>

                @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Create Enrollment</button>
    </form>
</div>
@endsection
