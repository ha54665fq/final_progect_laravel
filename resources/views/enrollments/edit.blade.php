@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Edit Enrollment</h2>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('enrollments.update', $enrollment->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="student_id" class="form-label">Student</label>
                            <select class="form-control @error('student_id') is-invalid @enderror"
                                    name="student_id" id="student_id" required>
                                <option value="">Select Student</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}"
                                        @if(old('student_id', $enrollment->student_id) == $student->id) selected @endif>
                                        {{ $student->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="course_id" class="form-label">Course</label>
                            <select class="form-control @error('course_id') is-invalid @enderror"
                                    name="course_id" id="course_id" required>
                                <option value="">Select Course</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}"
                                        @if(old('course_id', $enrollment->course_id) == $course->id) selected @endif>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Enrollment</button>
                            <a href="{{ route('enrollments.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
