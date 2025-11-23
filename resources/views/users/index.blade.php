@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Users</h2>

        <input type="text" id="search" class="form-control mb-3" placeholder="Search users...">

        <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Add User</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Weekly Hours</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->weekly_hours_limit }}</td>
                        <td>
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Delete this user?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        let timer;
        const input = document.getElementById('search');
        const tbody = document.getElementById('table-body');

        input.addEventListener('keyup', function() {
            clearTimeout(timer);
            timer = setTimeout(() => {
                fetch(`/users/search?query=${this.value}`)
                    .then(r => r.json())
                    .then(data => {
                        tbody.innerHTML = '';
                        data.forEach(user => {
                            tbody.innerHTML += `
                        <tr>
                            <td>${user.id}</td>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>${user.weekly_hours_limit}</td>
                            <td>
                                <a href="/users/${user.id}/edit" class="btn btn-sm btn-warning">Edit</a>
                                <form action="/users/${user.id}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</button>
                                </form>
                            </td>
                        </tr>`;
                        });
                    });
            }, 300);
        });
    </script>
@endsection
