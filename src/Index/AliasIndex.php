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

class AliasIndex extends AbstractIndex {
    /**
     * @param $q
     * @param array $filters
     * @param null $order
     *
     * @return Query
     */
    public function searchQuery($q, $filters = [], $order = null) {
        $qb = $this->createQueryBuilder();
        $qb->setQueryString($q);
        $qb->setDefaultField('content');

        $qb->addFilter('type', ['Alias']);
        foreach ($filters as $key => $values) {
            $qb->addFilter($key, $values);
        }
        $qb->addFacetField('maiden');
        $qb->setHighlightFields(['name', 'description', 'people']);

        if ($order) {
            $qb->setSorting($order);
        }

        return $qb->getQuery();
    }
}
