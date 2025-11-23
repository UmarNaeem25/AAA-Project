<?php

namespace App\Services;

use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;

class ShiftAlgorithmsService
{
    /**
     * ───────────────────────────────────────────────
     * GREEDY WEEKLY HOURS ALGORITHM
     * Checks if adding a shift exceeds user's weekly limit
     * ───────────────────────────────────────────────
     */
    public function greedyWeeklyLimit($userId, $date, $newDuration, $ignoreShiftId = null)
    {
        $weekStart = Carbon::parse($date)->startOfWeek();
        $weekEnd   = Carbon::parse($date)->endOfWeek();

        $shifts = Shift::where('user_id', $userId)
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->get();

        $total = 0;
        foreach ($shifts as $shift) {
            if ($ignoreShiftId && $shift->id == $ignoreShiftId) continue;
            $total += $shift->duration;
        }
        $total += $newDuration;

        $allowed = User::find($userId)->weekly_hours_limit ?? 0;

        return [
            'allowed' => $allowed,
            'required' => $total,
            'exceeds' => $total > $allowed
        ];
    }

    /**
     * ───────────────────────────────────────────────
     * INTERVAL OVERLAP CHECK
     * Checks if a shift overlaps with other shifts at same location
     * ───────────────────────────────────────────────
     */
    public function intervalOverlap($locationId, $date, $from, $to, $ignoreShiftId = null)
    {
        $shifts = Shift::where('location_id', $locationId)
            ->where('date', $date)
            ->get();

        foreach ($shifts as $shift) {
            if ($ignoreShiftId && $shift->id == $ignoreShiftId) continue;
            if ($shift->from < $to && $from < $shift->to) return true;
        }

        return false;
    }

    /**
     * ───────────────────────────────────────────────
     * SEARCH SHIFTS
     * Filter by location name or user name
     * ───────────────────────────────────────────────
     */
    public function searchShifts($locationName = null, $userName = null)
    {
        $shifts = Shift::with(['location', 'user'])->get();
        $results = [];

        foreach ($shifts as $shift) {
            if ($locationName && stripos($shift->location->name ?? '', $locationName) === false) continue;
            if ($userName && stripos($shift->user->name ?? '', $userName) === false) continue;
            $results[] = $shift;
        }

        return $results;
    }

    /**
     * ───────────────────────────────────────────────
     * BULK OPEN SHIFT ASSIGNMENT USING HUNGARIAN
     * Assigns all open shifts across all dates
     * ───────────────────────────────────────────────
     */
    public function hungarianAssignAllOpenShifts(): array
    {
        // 1. Fetch all open shifts
        $shifts = Shift::whereNull('user_id')
            ->orderBy('date')
            ->orderBy('from')
            ->get();

        if ($shifts->isEmpty()) return [];

        // 2. Fetch all users
        $users = User::where('id', '!=', 1)->get();
        if ($users->isEmpty()) return [];

        // 3. Assign shifts grouped by date
        $assignedShifts = [];
        $shiftsByDate = $shifts->groupBy('date');

        foreach ($shiftsByDate as $date => $shiftsOnDate) {
            $assignedShifts = array_merge($assignedShifts, $this->assignShiftsForDate($shiftsOnDate->values(), $users));
        }

        return $assignedShifts;
    }

    /**
     * Assign shifts for a single date using Hungarian algorithm
     */
    private function assignShiftsForDate($shifts, $users): array
    {
        $nShifts = $shifts->count();
        $nUsers = $users->count();
        $assignedShifts = [];

        // Build cost matrix
        $costMatrix = [];
        foreach ($shifts as $i => $shift) {
            $costMatrix[$i] = [];
            foreach ($users as $j => $user) {
                $wl = $this->greedyWeeklyLimit($user->id, $shift->date, $shift->duration);

                $overlap = Shift::where('user_id', $user->id)
                    ->where('date', $shift->date)
                    ->where('from', '<', $shift->to)
                    ->where('to', '>', $shift->from)
                    ->exists();

                $costMatrix[$i][$j] = ($wl['exceeds'] || $overlap) ? 1000 : 1;
            }
        }

        // Run Hungarian
        $assignments = $this->hungarianAlgorithm($costMatrix);

        // Save assignments
        foreach ($assignments as $shiftIndex => $userIndex) {
            if ($userIndex === null || $costMatrix[$shiftIndex][$userIndex] >= 1000) continue;

            $shift = $shifts[$shiftIndex];
            $shift->user_id = $users[$userIndex]->id;
            $shift->status = 'published';
            $shift->save();

            $assignedShifts[] = $shift;
        }

        return $assignedShifts;
    }

    /**
     * Simple greedy Hungarian placeholder
     */
    private function hungarianAlgorithm(array $matrix): array
    {
        $n = count($matrix);
        $m = count($matrix[0]);
        $usedUsers = [];
        $assignments = [];

        for ($i = 0; $i < $n; $i++) {
            $minCost = INF;
            $bestUser = null;
            for ($j = 0; $j < $m; $j++) {
                if (in_array($j, $usedUsers)) continue;
                if ($matrix[$i][$j] < $minCost) {
                    $minCost = $matrix[$i][$j];
                    $bestUser = $j;
                }
            }
            $assignments[$i] = $bestUser;
            if ($bestUser !== null) $usedUsers[] = $bestUser;
        }

        return $assignments;
    }
}
