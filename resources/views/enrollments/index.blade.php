@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h2 class="float-start">Course Enrollments</h2>
                    @can('manage-enrollments')
                        <a href="{{ route('enrollments.create') }}" class="btn btn-primary float-end">Add New Enrollment</a>
                    @endcan
                </div>
                <div class="card-body">
                    @if($enrollments->isEmpty())
                        <p class="text-center">No enrollments found.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Enrolled At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enrollments as $enrollment)
                                    <tr>
                                        <td>{{ $enrollment->student->name }}</td>
                                        <td>{{ $enrollment->course->title }}</td>
                                        <td>{{ $enrollment->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @can('manage-enrollments')
                                                <a href="{{ route('enrollments.edit', $enrollment->id) }}"
                                                   class="btn btn-sm btn-primary">Edit</a>

                                                <form action="{{ route('enrollments.destroy', $enrollment->id) }}"
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure you want to remove this enrollment?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
