<?php

declare(strict_types=1);

namespace App\Services;

class MeilisearchHelper {
    public static function getDefaultSortOptions() : array {
        return ['Relevance' => null, 'Name (A to Z)' => 'sortable:asc', 'Name (Z to A)' => 'sortable:desc'];
    }
    public static function getSort(array $sortOptions, string $order) : ?array {
        if (in_array($order, array_values($sortOptions)) && $order) {
            return [$order];
        }
        return null;
    }


    public static function generateTermFilter(string $fieldName, array $terms) : array {
        $filters = [];
        foreach ($terms as $term) {
            $filters[] = "({$fieldName} = '{$term}')";
        }
        return $filters;
    }

    public static function rangeFilter(int $start, int $end, int $gap, bool $includePreStart = false, bool $includePostEnd = false) : array {
        $ranges = [];
        if ($includePreStart) {
            $ranges[] = [
                'to' => $start - 1,
            ];
        }
        foreach (range($start, $end, $gap) as $from) {
            $to = $from + $gap - 1;
            if ($to > $end) {
                $to = $end;
            }
            $ranges[] = [
                'from' => $from,
                'to' => $to,
            ];
        }
        if ($includePostEnd) {
            $ranges[] = [
                'from' => $end + 1,
            ];
        }

        return $ranges;
    }

    public static function generateRangeFilter(string $fieldName, array $ranges, array $selectedIndexes) : array {
        $filters = [];
        foreach ($selectedIndexes as $selectedIndex) {
            if (array_key_exists($selectedIndex, $ranges)) {
                $range = $ranges[$selectedIndex];
                if (array_key_exists('from', $range) && array_key_exists('to', $range)) {
                    $filters[] = "({$fieldName} <= {$range['to']} AND {$fieldName} >= {$range['from']})";
                } elseif (array_key_exists('to', $range)) {
                    $filters[] = "({$fieldName} <= {$range['to']})";
                } elseif (array_key_exists('from', $range)) {
                    $filters[] = "({$fieldName} <= {$range['from']})";
                }
            }
        }
        return $filters;
    }

    public static function addRangeFilterCounts(array $ranges, array $values) : array {
        $in_range = function(?int $to, ?int $from, int $value) : bool {
            if (!is_null($to) && $to < $value) {
                return false;
            }
            if (!is_null($from) && $from > $value) {
                return false;
            }
            return true;
        };

        foreach ($ranges as $index=>$range) {
            $ranges[$index]['count'] = array_sum(array_filter(
                $values,
                fn(int $key) => $in_range($range['to'] ?? null, $range['from'] ?? null, $key),
                ARRAY_FILTER_USE_KEY
            ));
        }
        return $ranges;
    }
}
