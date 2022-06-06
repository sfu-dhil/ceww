<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Index;

use Nines\SolrBundle\Index\AbstractIndex;
use Solarium\QueryType\Select\Query\Query;

class DefaultIndex extends AbstractIndex {
    /**
     * @param $q
     * @param $filters
     * @param $order
     *
     * @return Query
     */
    public function searchQuery($q, $filters, $order = null) {
        $qb = $this->createQueryBuilder();
        $qb->setQueryString($q);
        $qb->setDefaultField('content');

        foreach ($filters as $key => $values) {
            $qb->addFilter($key, $values);
        }
        $qb->setHighlightFields('content');
        $qb->addFacetField('type');

        if ($order) {
            $qb->setSorting($order);
        }

        $qb->setHighlightFields(['name', 'fullName', 'aliases', 'title', 'description',
            'genres', 'location',  'places', 'regionName', 'countryName', 'contributions',
            'residences', 'birthPlace', 'deathPlace', ]);

        return $qb->getQuery();
    }
}
