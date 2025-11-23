@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create Location</h2>
        <form action="{{ route('locations.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Location Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" name="address" class="form-control">
            </div>
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('locations.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
