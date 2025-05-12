@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Enrollment</h1>

    <form action="{{ route('enrollments.update', $enrollment->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="student_id">Student</label>
            <select class="form-control" name="student_id" required>
               
                @foreach ($students as $student)
                    <option value="{{ $student->id }}" @if($student->id == $enrollment->student_id) selected @endif>
                        {{ $student->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="course_id">Course</label>
            <select class="form-control" name="course_id" required>

                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" @if($course->id == $enrollment->course_id) selected @endif>
                        {{ $course->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Enrollment</button>
    </form>
</div>
@endsection
