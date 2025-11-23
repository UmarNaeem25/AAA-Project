<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\ShiftRequest;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $assignedShifts = Shift::with('location', 'role')
            ->where('user_id', $user->id)
            ->orderBy('date')
            ->get();

        $openShifts = Shift::with('location', 'role')
            ->whereNull('user_id')
            ->where('role_id', $user->role_id)
            ->orderBy('date')
            ->get();

        return view('schedules.index', compact('assignedShifts', 'openShifts'));
    }

    public function requestShift(Shift $shift)
    {
        $user = Auth::user();

        $existing = ShiftRequest::where('shift_id', $shift->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'You have already requested this shift.');
        }

        ShiftRequest::create([
            'shift_id' => $shift->id,
            'user_id' => $user->id,
        ]);

        return back()->with('success', 'Shift request submitted.');
    }
}
