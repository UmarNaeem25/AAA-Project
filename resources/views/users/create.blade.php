@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create User</h2>
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role_id" class="form-select" required>
                    <option value="">Select Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Weekly Hours Limit</label>
                <input type="number" name="weekly_hours_limit" class="form-control" value="40" required>
            </div>
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
