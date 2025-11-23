@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Location</h2>
        <form action="{{ route('locations.update', $location->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Location Name</label>
                <input type="text" name="name" class="form-control" value="{{ $location->name }}" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" name="address" class="form-control" value="{{ $location->address }}">
            </div>
            <button class="btn btn-primary">Update</button>
            <a href="{{ route('locations.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
