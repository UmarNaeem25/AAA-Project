<?php

namespace App\Services;

class SearchService
{
    public function linearSearch($items, array $columns, string $query): array
    {
        $result = [];
        foreach ($items as $item) {
            foreach ($columns as $col) {
                if (
                    (is_array($item) && isset($item[$col]) && stripos((string)$item[$col], $query) !== false)
                    || (is_object($item) && isset($item->$col) && stripos((string)$item->$col, $query) !== false)
                ) {
                    $result[] = is_array($item) ? $item : (array) $item;
                    break;
                }
            }
        }
        return $result;
    }

    public function mergeSort(array $array, string $sortColumn): array
    {
        if (count($array) <= 1) {
            return $array;
        }
        $mid = (int) (count($array) / 2);
        $left = array_slice($array, 0, $mid);
        $right = array_slice($array, $mid);
        return $this->merge(
            $this->mergeSort($left, $sortColumn),
            $this->mergeSort($right, $sortColumn),
            $sortColumn
        );
    }

    private function merge(array $left, array $right, string $sortColumn): array
    {
        $result = [];

        while ($left && $right) {
            $l = $left[0][$sortColumn] ?? null;
            $r = $right[0][$sortColumn] ?? null;

            if ($l !== null && $r !== null) {
                if ($sortColumn === 'id') {
                    if ((int)$l <= (int)$r) {
                        $result[] = array_shift($left);
                    } else {
                        $result[] = array_shift($right);
                    }
                } else {
                    if (strcasecmp((string)$l, (string)$r) <= 0) {
                        $result[] = array_shift($left);
                    } else {
                        $result[] = array_shift($right);
                    }
                }
            } else {
                $result[] = array_shift($left ?: $right);
            }
        }

        return array_merge($result, $left, $right);
    }

    public function searchAndSort(array $items, string $query, array $columns, string $sortColumn): array
    {
        $filtered = $this->linearSearch($items, $columns, $query);
        return $this->mergeSort(array_values($filtered), $sortColumn);
    }
}
