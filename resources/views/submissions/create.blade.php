@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Submission</h1>

    <form action="{{ route('submissions.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="assignment_id">Assignment</label>
            <select class="form-control" name="assignment_id" required>
             
                @foreach ($assignments as $assignment)
                    <option value="{{ $assignment->id }}">{{ $assignment->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="student_id">Student</label>
            <select class="form-control" name="student_id" required>

                @foreach ($students as $student)
                    <option value="{{ $student->id }}">{{ $student->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="file_path">File</label>
            <input type="file" class="form-control" name="file_path" required>
        </div>

        <div class="form-group">
            <label for="submitted_at">Submitted At</label>
            <input type="date" class="form-control" name="submitted_at">
        </div>

        <div class="form-group">
            <label for="grade">Grade</label>
            <input type="number" class="form-control" name="grade" step="any">
        </div>

        <button type="submit" class="btn btn-primary">Create Submission</button>
    </form>
</div>
@endsection
