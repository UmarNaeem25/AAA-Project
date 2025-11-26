<?php

namespace App\Services;

use Carbon\Carbon;

class ShiftAlgorithmsService
{
    public function greedyWeeklyLimit($user, $existingShifts, $newShiftDuration)
    {
        $total = 0;

        foreach ($existingShifts as $shift) {
            $total += $shift->duration;
        }

        $total += $newShiftDuration;

        $allowed = $user->weekly_hours_limit ?? 0;

        return [
            'allowed'  => $allowed,
            'required' => $total,
            'exceeds'  => $total > $allowed
        ];
    }

    public function intervalOverlap($existingShifts, $from, $to)
    {
        foreach ($existingShifts as $shift) {
            if ($shift->from < $to && $from < $shift->to) {
                return true;
            }
        }
        return false;
    }

   public function searchShifts($allShifts, $locationName = null, $userName = null)
    {
        $results = [];

        foreach ($allShifts as $shift) {

            $locationMatch = true;
            $userMatch = true;

            if ($locationName) {
                $loc = strtolower($shift->location->name ?? '');
                $locationMatch = str_contains($loc, strtolower($locationName));
            }

     
            if ($userName) {
                $usr = strtolower($shift->user->name ?? '');
                $userMatch = str_contains($usr, strtolower($userName));
            }

            if ($locationMatch && $userMatch) {
                $results[] = $shift;
            }
        }

        return $results;
    }


    public function hungarianAssignAllOpenShifts($openShifts, $users, $allShifts)
    {
        $assigned = [];

        $groups = $openShifts->groupBy('date');

        foreach ($groups as $date => $shiftsOnDate) {
            $assigned = array_merge(
                $assigned,
                $this->assignShiftsForDate(
                    $shiftsOnDate->values(),
                    $users,
                    $allShifts
                )
            );
        }

        return $assigned;
    }

    private function assignShiftsForDate($shifts, $users, $allShifts)
    {
        $countShifts = $shifts->count();
        $countUsers  = $users->count();

        $costMatrix = [];

        foreach ($shifts as $i => $shift) {
            $costMatrix[$i] = [];

            foreach ($users as $j => $user) {
                $userShifts = $allShifts->where('user_id', $user->id);

                $weekStart = Carbon::parse($shift->date)->startOfWeek();
                $weekEnd   = Carbon::parse($shift->date)->endOfWeek();

                $weeklyShifts = $userShifts
                    ->whereBetween('date', [$weekStart, $weekEnd])
                    ->where('id', '!=', $shift->id);

                $weeklyCheck = $this->greedyWeeklyLimit(
                    $user,
                    $weeklyShifts,
                    $shift->duration
                );

                $dayShifts = $userShifts
                    ->where('date', $shift->date)
                    ->where('id', '!=', $shift->id);

                $overlap = $this->intervalOverlap(
                    $dayShifts,
                    $shift->from,
                    $shift->to
                );

                $costMatrix[$i][$j] = ($weeklyCheck['exceeds'] || $overlap) ? 1000 : 1;
            }
        }

        $assignments = $this->hungarian($costMatrix);

        $result = [];

        foreach ($assignments as $shiftIndex => $userIndex) {
            if ($userIndex === null) continue;
            if ($costMatrix[$shiftIndex][$userIndex] >= 1000) continue;

            $shift = $shifts[$shiftIndex];
            $user  = $users[$userIndex];

            $shift->user_id = $user->id;
            $shift->status = 'published';
            $shift->save();

            $result[] = $shift;
        }

        return $result;
    }

    private function hungarian(array $matrix)
    {
        $n = count($matrix);
        $m = count($matrix[0]);
        $used = [];
        $assign = [];

        for ($i = 0; $i < $n; $i++) {
            $min = INF;
            $best = null;

            for ($j = 0; $j < $m; $j++) {
                if (in_array($j, $used)) continue;
                if ($matrix[$i][$j] < $min) {
                    $min = $matrix[$i][$j];
                    $best = $j;
                }
            }

            $assign[$i] = $best;
            if ($best !== null) $used[] = $best;
        }

        return $assign;
    }
}
