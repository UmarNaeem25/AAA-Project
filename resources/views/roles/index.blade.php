@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Roles</h2>

        <input type="text" id="search" class="form-control mb-3" placeholder="Search roles">

        <select id="sort" class="form-control mb-3 d-none" style="width:200px;">
            <option value="name">Sort by Name</option>
            <option value="id">Sort by ID</option>
        </select>


        <a href="{{ route('roles.create') }}" class="btn btn-primary mb-3">Add Role</a>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="table-body">
                @foreach ($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>{{ $role->name }}</td>
                        <td>
                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

   <script>
        function loadData() {
            let keyword = encodeURIComponent(document.getElementById('search').value);
            let sort = encodeURIComponent(document.getElementById('sort').value);
            let url = `{{ route('roles.search') }}?query=${keyword}&sort=${sort}`;

            fetch(url, {
                headers: { "X-Requested-With": "XMLHttpRequest" }
            })
            .then(res => res.json())
            .then(data => {
                let rows = '';
                data.forEach(role => {
                    rows += `
                        <tr>
                            <td>${role.id}</td>
                            <td>${role.name}</td>
                            <td>
                                <a href="/roles/${role.id}/edit" class="btn btn-sm btn-warning">Edit</a>
                                <form action="/roles/${role.id}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    `;
                });
                document.getElementById('table-body').innerHTML = rows;
            });
        }

        document.getElementById('search').addEventListener('keyup', loadData);
        document.getElementById('sort').addEventListener('change', loadData);
        </script>


@endsection
