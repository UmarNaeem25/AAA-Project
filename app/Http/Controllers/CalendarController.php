<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Shift;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\ShiftAlgorithmsService;
use Carbon\Carbon;

class CalendarController extends Controller
{
    protected $algo;

    public function __construct(ShiftAlgorithmsService $algo)
    {
        $this->algo = $algo;
    }

    public function index(Request $request)
    {
        $locations = Location::all();
        $users = User::where('id', '!=', 1)->get();

        return view('calendar.index', [
            'locationFilter' => $request->location,
            'locations'      => $locations,
            'users'          => $users,
        ]);
    }

    public function create(Request $request)
    {
        $locations = Location::all();
        $roles = Role::all();
        $users = User::where('id', '!=', 1)->get();

        $shift = $request->id ? Shift::find($request->id) : null;
        $date = $shift ? $shift->date : $request->date;

        return view('calendar.create', compact('date', 'locations', 'roles', 'users', 'shift'));
    }

    public function events(Request $request)
    {
        $allShifts = Shift::with(['location', 'user'])->get();

        $shifts = $this->algo->searchShifts(
            allShifts: $allShifts,
            locationName: $request->location,
            userName: $request->user
        );

        $events = collect($shifts)->map(function ($shift) {
            return [
                'id'    => $shift->id,
                'start' => $shift->date,
                'title' => ($shift->user->name ?? "Open Shift") . " - " .
                           ($shift->location->name ?? "N/A") . " - " .
                           $shift->from . " to " . $shift->to,
                'color' => $shift->user_id ? '#e2af4a' : '#57a8cd'
            ];
        });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date'        => 'required|date',
            'from'        => 'required',
            'to'          => 'required',
            'duration'    => 'required|numeric|min:0',
            'break_time'  => 'required|numeric|min:0',
            'location_id' => 'required|exists:locations,id',
            'role_id'     => 'required|exists:roles,id',
            'user_id'     => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $shiftId = $request->id;

        if ($request->user_id) {
            $user = User::find($request->user_id);
            $weekStart = Carbon::parse($request->date)->startOfWeek();
            $weekEnd = Carbon::parse($request->date)->endOfWeek();

            $existingShifts = Shift::where('user_id', $user->id)
                ->whereBetween('date', [$weekStart, $weekEnd])
                ->where('id', '!=', $shiftId)
                ->get();

            $check = $this->algo->greedyWeeklyLimit(
                $user,
                $existingShifts,
                $request->duration
            );

            if ($check['exceeds']) {
                return response()->json([
                    'success'  => false,
                    'message'  => 'Weekly hours exceeded',
                    'allowed'  => $check['allowed'],
                    'required' => $check['required']
                ], 422);
            }
        }

        $userShifts = collect();
        if ($request->user_id) {
            $userShifts = Shift::where('user_id', $request->user_id)
                ->where('date', $request->date)
                ->where('id', '!=', $shiftId)
                ->get();
        }

        $locationShifts = Shift::where('location_id', $request->location_id)
            ->where('date', $request->date)
            ->where('id', '!=', $shiftId)
            ->get();

        $shiftsToCheck = $userShifts->merge($locationShifts);

        $overlap = $this->algo->intervalOverlap(
            $shiftsToCheck,
            $request->from,
            $request->to,
            $request->user_id,
            $request->location_id
        );

        if ($overlap) {
            return response()->json([
                'success' => false,
                'message' => 'Shift time overlaps with another shift for this user or location'
            ], 422);
        }

        DB::beginTransaction();

        try {
            $data = [
                'location_id' => $request->location_id,
                'role_id'     => $request->role_id,
                'user_id'     => $request->user_id,
                'from'        => $request->from,
                'to'          => $request->to,
                'duration'    => $request->duration,
                'break_time'  => $request->break_time,
                'date'        => $request->date,
                'status'      => $request->user_id ? 'published' : 'open'
            ];

            $shift = $shiftId
                ? tap(Shift::findOrFail($shiftId))->update($data)
                : Shift::create($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Shift saved successfully',
                'shift'   => $shift
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy(Shift $shift)
    {
        $shift->delete();
        return response()->json(['success' => true]);
    }

    public function assignAllOpenShifts(Request $request)
    {
        $openShifts = Shift::whereNull('user_id')->get();
        $users = User::where('id', '!=', 1)->get();
        $allShifts = Shift::with('location')->get();

        $assigned = $this->algo->hungarianAssignAllOpenShifts(
            openShifts: $openShifts,
            users: $users,
            allShifts: $allShifts
        );

        return response()->json([
            'success' => true,
            'assignedShifts' => $assigned,
            'message' => count($assigned) . ' open shifts assigned successfully.'
        ]);
    }
}
