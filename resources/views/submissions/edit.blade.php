@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Submission</h1>

    <form action="{{ route('submissions.update', $submission->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="assignment_id">Assignment</label>
            <select class="form-control" name="assignment_id" required>
            
                @foreach ($assignments as $assignment)
                    <option value="{{ $assignment->id }}" @if($assignment->id == $submission->assignment_id) selected @endif>
                        {{ $assignment->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="student_id">Student</label>
            <select class="form-control" name="student_id" required>

                @foreach ($students as $student)
                    <option value="{{ $student->id }}" @if($student->id == $submission->student_id) selected @endif>
                        {{ $student->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="file_path">File</label>
            <input type="file" class="form-control" name="file_path">
        </div>

        <div class="form-group">
            <label for="submitted_at">Submitted At</label>
            <input type="date" class="form-control" name="submitted_at" value="{{ old('submitted_at', $submission->submitted_at->toDateString()) }}">
        </div>

        <div class="form-group">
            <label for="grade">Grade</label>
            <input type="number" class="form-control" name="grade" step="any" value="{{ old('grade', $submission->grade) }}">
        </div>

        <button type="submit" class="btn btn-primary">Update Submission</button>
    </form>
</div>
@endsection
