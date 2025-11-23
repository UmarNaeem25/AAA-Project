@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Locations</h2>

    <input type="text" id="search" class="form-control mb-3" placeholder="Search locations...">

    <a href="{{ route('locations.create') }}" class="btn btn-primary mb-3">Add Location</a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="table-body">
            @foreach ($locations as $location)
                <tr>
                    <td>{{ $location->id }}</td>
                    <td>{{ $location->name }}</td>
                    <td>{{ $location->address }}</td>
                    <td>
                        <a href="{{ route('locations.edit', $location->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('locations.destroy', $location->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete this location?')">Delete</button>
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
        fetch(`/locations/search?query=${this.value}`)
            .then(r => r.json())
            .then(data => {
                tbody.innerHTML = '';
                data.forEach(loc => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${loc.id}</td>
                            <td>${loc.name}</td>
                            <td>${loc.address ?? ''}</td>
                            <td>
                                <a href="/locations/${loc.id}/edit" class="btn btn-sm btn-warning">Edit</a>
                                <form action="/locations/${loc.id}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this location?')">Delete</button>
                                </form>
                            </td>
                        </tr>`;
                });
            });
    }, 300);
});
</script>

@endsection
