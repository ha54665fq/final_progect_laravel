@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h2>Edit User</h2>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
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

                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="name">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        @if(auth()->user()->isAdmin())
                            <div class="form-group mb-3">
                                <label for="role">Role</label>
                                <select class="form-control @error('role') is-invalid @enderror" name="role" required>
                                    <option value="student" @if(old('role', $user->role) == 'student') selected @endif>Student</option>
                                    <option value="teacher" @if(old('role', $user->role) == 'teacher') selected @endif>Teacher</option>
                                    <option value="admin" @if(old('role', $user->role) == 'admin') selected @endif>Admin</option>
                                </select>
                                @error('role')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        <div class="form-group mb-3">
                            <label for="password">New Password (leave blank to keep current)</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   name="password">
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password_confirmation">Confirm New Password</label>
                            <input type="password" class="form-control" name="password_confirmation">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update User</button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
