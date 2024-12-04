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


    // public function getElasticQuery(?string $q = null, ?string $sortKey = null, array $searchFilters = []) : Query {
    //     // search query
    //     $boolQuery = new Query\BoolQuery();

    //     // query term fields
    //     if (array_key_exists('queryTermFields', $this->elasticSearchOptions)) {
    //         $keywordQuery = new Query\QueryString($q ? $q : '*');
    //         $keywordQuery->setDefaultOperator('or');
    //         $keywordQuery->setFields($this->elasticSearchOptions['queryTermFields']);
    //         $boolQuery->addShould($keywordQuery);
    //     }

    //     // geo search
    //     foreach ($this->elasticSearchOptions['queryGeoDistanceFields'] ?? [] as $field => $options) {
    //         $distanceFilter = new Query\GeoDistance($field, $options['location'], $options['distance']);
    //         $boolQuery->addFilter($distanceFilter);
    //     }

    //     // exclude values
    //     if (array_key_exists('excludeValues', $this->elasticSearchOptions)) {
    //         foreach ($this->elasticSearchOptions['excludeValues'] as $field => $terms) {
    //             $termsQuery = new Query\Terms($field, $terms);
    //             $boolQuery->addMustNot($termsQuery);
    //         }
    //     }

    //     // overall query
    //     $query = new Query($boolQuery);

    //     // highlights
    //     $query->setTrackScores(true);
    //     if (array_key_exists('highlights', $this->elasticSearchOptions)) {
    //         $query->setHighlight([
    //             'pre_tags' => '<span class="hl">',
    //             'post_tags' => '</span>',
    //             'number_of_fragments' => 1,
    //             'fields' => $this->elasticSearchOptions['highlights'],
    //         ]);
    //     }

    //     // sorting
    //     if (array_key_exists($sortKey ?? '', $this->elasticSearchOptions['sort'] ?? [])) {
    //         $sortField = $this->elasticSearchOptions['sort'][$sortKey];
    //         $options = $sortField['options'] ?? [];
    //         $query->setSort([$sortField['field'] => $options]);
    //     }

    //     $boolPostFilterQuery = new Query\BoolQuery();
    //     $hasPostFilter = false;
    //     if (array_key_exists('rangeFilters', $this->elasticSearchOptions)) {
    //         foreach ($this->elasticSearchOptions['rangeFilters'] as $name => $rangeFilter) {
    //             $aggregation = new Aggregation\Range($name);
    //             $aggregation->setField($rangeFilter['field']);
    //             $aggregation->setParam('ranges', $rangeFilter['ranges']);
    //             $query->addAggregation($aggregation);

    //             if (array_key_exists($name, $searchFilters)) {
    //                 $boolRangeFilterQuery = new Query\BoolQuery();

    //                 $rangeIndexes = $searchFilters[$name];
    //                 foreach ($rangeIndexes as $rangeIndex) {
    //                     if (array_key_exists($rangeIndex, $rangeFilter['ranges'])) {
    //                         $rangeValues = $rangeFilter['ranges'][$rangeIndex];
    //                         $filter = [];
    //                         if (array_key_exists('from', $rangeValues)) {
    //                             $filter['gte'] = $rangeValues['from'];
    //                         }
    //                         if (array_key_exists('to', $rangeValues)) {
    //                             $filter['lte'] = $rangeValues['to'];
    //                         }
    //                         $rangeQuery = new Query\Range($rangeFilter['field'], $filter);
    //                         $boolRangeFilterQuery->addShould($rangeQuery);
    //                     }
    //                 }
    //                 $hasPostFilter = true;
    //                 $boolPostFilterQuery->addMust($boolRangeFilterQuery);
    //             }
    //         }
    //     }
    //     if (array_key_exists('filters', $this->elasticSearchOptions)) {
    //         foreach ($this->elasticSearchOptions['filters'] as $name => $filter) {
    //             $aggregation = new Aggregation\Terms($name);
    //             $aggregation->setField($filter['field']);
    //             $aggregation->setSize($filter['size']);
    //             $aggregation->setOrder('_count', 'desc');
    //             $aggregation->setMinimumDocumentCount(1);
    //             $query->addAggregation($aggregation);

    //             if (array_key_exists($name, $searchFilters)) {
    //                 $boolRangeFilterQuery = new Query\BoolQuery();
    //                 $termsQuery = new Query\Terms($filter['field'], $searchFilters[$name]);
    //                 $hasPostFilter = true;
    //                 $boolPostFilterQuery->addMust($termsQuery);
    //             }
    //         }
    //     }
    //     if ($hasPostFilter) {
    //         $query->setPostFilter($boolPostFilterQuery);
    //     }

    //     return $query;
    // }
}
