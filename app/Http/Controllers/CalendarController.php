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

        $shift = $request->has('id') ? Shift::find($request->id) : null;
        $date = $shift ? $shift->date : $request->date;

        return view('calendar.create', compact('date', 'locations', 'roles', 'users', 'shift'));
    }

    public function events(Request $request)
    {
        $shifts = $this->algo->searchShifts(
            locationName: $request->location ?? null,
            userName:     $request->user ?? null
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
            $check = $this->algo->greedyWeeklyLimit(
                userId: $request->user_id,
                date: $request->date,
                newDuration: $request->duration,
                ignoreShiftId: $shiftId
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

        $overlap = $this->algo->intervalOverlap(
            locationId: $request->location_id,
            date: $request->date,
            from: $request->from,
            to: $request->to,
            ignoreShiftId: $shiftId
        );

        if ($overlap) {
            return response()->json([
                'success' => false,
                'message' => 'Shift time overlaps with another shift at this location'
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
        $assignedShifts = $this->algo->hungarianAssignAllOpenShifts();

        return response()->json([
            'success' => true,
            'assignedShifts' => $assignedShifts,
            'message' => count($assignedShifts) . ' open shifts assigned successfully.'
        ]);
    }


}
