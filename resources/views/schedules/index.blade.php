@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Assigned Shifts</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Role</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($assignedShifts as $shift)
                    <tr>
                        <td>{{ $shift->date }}</td>
                        <td>{{ $shift->from }} - {{ $shift->to }}</td>
                        <td>{{ $shift->location->name }}</td>
                        <td>{{ $shift->role->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h2>Open Shifts</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($openShifts as $shift)
                    <tr>
                        <td>{{ $shift->date }}</td>
                        <td>{{ $shift->from }} - {{ $shift->to }}</td>
                        <td>{{ $shift->location->name }}</td>
                        <td>{{ $shift->role->name }}</td>
                        <td>
                            <form action="{{ route('schedules.request', $shift) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">Request</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
