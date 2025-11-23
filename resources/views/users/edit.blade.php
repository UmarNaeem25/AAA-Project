@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit User</h2>
        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role_id" class="form-select" required>
                    <option value="">Select Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Weekly Hours Limit</label>
                <input type="number" name="weekly_hours_limit" class="form-control" value="{{ $user->weekly_hours_limit }}"
                    required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password (leave blank to keep current)</label>
                <input type="password" name="password" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>

            <button class="btn btn-primary">Update</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
