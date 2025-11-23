@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Dashboard</h1>

        <div class="row">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Users</h5>
                        <p class="card-text">Manage users and their roles.</p>
                        <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">Go</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Locations</h5>
                        <p class="card-text">Manage locations for shifts.</p>
                        <a href="{{ route('locations.index') }}" class="btn btn-light btn-sm">Go</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Roles</h5>
                        <p class="card-text">Manage roles for users.</p>
                        <a href="{{ route('roles.index') }}" class="btn btn-light btn-sm">Go</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
