<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Index;

use Nines\SolrBundle\Index\AbstractIndex;
use Solarium\QueryType\Select\Query\Query;

class PublicationIndex extends AbstractIndex {
    /**
     * @param $q
     * @param array $filters
     * @param array $rangeFilters
     * @param null $order
     *
     * @return Query
     */
    public function searchQuery($q, $filters = [], $rangeFilters = [], $order = null) {
        $year = date('Y');
        $qb = $this->createQueryBuilder();
        $qb->setQueryString($q . ' AND (type_s:Book OR type_s:Compilation OR type_s:Periodical)');
        $qb->setDefaultField('content');

        foreach ($filters as $key => $values) {
            $qb->addFilter($key, $values);
        }
        foreach ($rangeFilters as $key => $values) {
            foreach ($values as $v) {
                list($start, $end) = explode(' ', $v);
                $qb->addFilterRange($key, $start, $end);
            }
        }

        $qb->addFacetField('type');
        $qb->addFacetField('location_fct');
        $qb->addFacetRange('dateYear', 1700, $year, 50);

        $qb->setHighlightFields(['title', 'description', 'location', 'genres','continuedBy', 'continuedFrom']);

        if ($order) {
            $qb->setSorting($order);
        }

        return $qb->getQuery();
    }
}
