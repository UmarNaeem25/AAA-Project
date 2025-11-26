<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class ShiftAlgorithmsService
{
    /**
     * ───────────────────────────────────────────────
     * GREEDY WEEKLY LIMIT (No DB queries)
     * ───────────────────────────────────────────────
     * @param  User $user
     * @param  Collection $existingShifts   (all shifts for this user for the week)
     * @param  int $newShiftDuration
     * @return array
     */
    public function greedyWeeklyLimit($user, Collection $existingShifts, int $newShiftDuration)
    {
        $totalHours = $existingShifts->sum('duration') + $newShiftDuration;
        $allowed = $user->weekly_hours_limit ?? 0;

        return [
            'allowed'  => $allowed,
            'required' => $totalHours,
            'exceeds'  => $totalHours > $allowed
        ];
    }

    /**
     * ───────────────────────────────────────────────
     * INTERVAL OVERLAP CHECK (No DB queries)
     * ───────────────────────────────────────────────
     * @param  Collection $existingShifts (all shifts at this location for this date)
     * @param  string $from
     * @param  string $to
     * @param  int|null $ignoreId
     * @return bool
     */
    public function intervalOverlap(Collection $existingShifts, string $from, string $to, $ignoreId = null)
    {
        foreach ($existingShifts as $s) {
            if ($ignoreId && $s->id == $ignoreId) continue;

            if ($s->from < $to && $from < $s->to) {
                return true;
            }
        }
        return false;
    }

    /**
     * ───────────────────────────────────────────────
     * SEARCH SHIFTS (No DB queries)
     * ───────────────────────────────────────────────
     * @param  Collection $allShifts
     * @param  string|null $locationName
     * @param  string|null $userName
     * @return Collection
     */
    public function searchShifts(Collection $allShifts, $locationName = null, $userName = null)
    {
        return $allShifts->filter(function ($shift) use ($locationName, $userName) {

            if ($locationName && stripos($shift->location->name ?? '', $locationName) === false) {
                return false;
            }
            if ($userName && stripos($shift->user->name ?? '', $userName) === false) {
                return false;
            }
            return true;
        });
    }

    /**
     * ───────────────────────────────────────────────
     * BULK OPEN SHIFT ASSIGNMENT USING HUNGARIAN
     * (Algorithmic Only — no DB)
     * ───────────────────────────────────────────────
     * @param Collection $openShifts   (all shifts with user_id = null)
     * @param Collection $users
     * @param Collection $allShifts    (all shifts in the system)
     * @return array  (assigned shifts with updated user_id/status)
     */
    public function hungarianAssignAllOpenShifts(
        Collection $openShifts,
        Collection $users,
        Collection $allShifts
    ): array
    {
        if ($openShifts->isEmpty()) return [];

        $assigned = [];

        // group open shifts by date
        $grouped = $openShifts->groupBy('date');

        foreach ($grouped as $date => $shiftsOnDate) {
            $assigned = array_merge(
                $assigned,
                $this->assignShiftsForDate($shiftsOnDate->values(), $users, $allShifts)
            );
        }

        return $assigned;
    }

    /**
     * Assign shifts for a single date
     * @param Collection $shifts
     * @param Collection $users
     * @param Collection $allShifts
     * @return array
     */
    private function assignShiftsForDate(Collection $shifts, Collection $users, Collection $allShifts): array
    {
        $nShifts = $shifts->count();
        $nUsers  = $users->count();

        $costMatrix = [];

        foreach ($shifts as $i => $shift) {

            $costMatrix[$i] = [];

            foreach ($users as $j => $user) {

                // Weekly limit — provide this user's shifts for same week
                $weekStart = Carbon::parse($shift->date)->startOfWeek();
                $weekEnd   = Carbon::parse($shift->date)->endOfWeek();

                $userWeeklyShifts = $allShifts->filter(function ($s) use ($user, $weekStart, $weekEnd) {
                    return $s->user_id == $user->id &&
                           $s->date >= $weekStart->toDateString() &&
                           $s->date <= $weekEnd->toDateString();
                });

                $wl = $this->greedyWeeklyLimit($user, $userWeeklyShifts, $shift->duration);

                // Overlap — check user’s shifts for this date
                $userShiftsSameDay = $allShifts->filter(function ($s) use ($user, $shift) {
                    return $s->user_id == $user->id && $s->date == $shift->date;
                });

                $overlap = false;
                foreach ($userShiftsSameDay as $s) {
                    if ($s->from < $shift->to && $shift->from < $s->to) {
                        $overlap = true;
                        break;
                    }
                }

                // High cost for invalid assignment
                $costMatrix[$i][$j] = ($wl['exceeds'] || $overlap) ? 1000 : 1;
            }
        }

        // run simplified Hungarian (greedy)
        $assignments = $this->hungarianAlgorithm($costMatrix);

        // apply assignments only in memory
        $applied = [];

        foreach ($assignments as $shiftIndex => $userIndex) {

            if ($userIndex === null) continue;
            if ($costMatrix[$shiftIndex][$userIndex] >= 1000) continue;

            $shift = $shifts[$shiftIndex];
            $shift->user_id = $users[$userIndex]->id;
            $shift->status = 'published';

            $applied[] = $shift;
        }

        return $applied;
    }

    /**
     * Simplified greedy Hungarian matcher (still O(n²))
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
            if ($bestUser !== null) {
                $usedUsers[] = $bestUser;
            }
        }

        return $assignments;
    }
}
