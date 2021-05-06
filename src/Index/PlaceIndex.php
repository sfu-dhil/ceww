<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Index;

use App\Entity\Place;
use Nines\SolrBundle\Index\AbstractIndex;
use Solarium\QueryType\Select\Query\Query;

class PlaceIndex extends AbstractIndex
{

    /**
     * @param $q
     * @param array $filters
     * @param array $rangeFilters
     * @param null $order
     *
     * @return Query
     */
    public function searchQuery($q, $filters = [], $order = null) {
        $qb = $this->createQueryBuilder();
        $qb->setQueryString($q);
        $qb->setDefaultField('content');

        $qb->addFilter('type', ['Place']);
        foreach ($filters as $key => $values) {
            $qb->addFilter($key, $values);
        }
        $qb->addFacetField('countryName');
        $qb->addFacetField('regionName');

        $qb->setHighlightFields('content');

        if($order) {
            $qb->setSorting($order);
        }

        return $qb->getQuery();
    }

    public function nearbyQuery(Place $place, $distance) {
        if( ! $place->getCoordinates()) {
            return null;
        }
        $qb = $this->createQueryBuilder();
        $qb->addGeographicFilter('coordinates', $place->getLatitude(), $place->getLongitude(), "$distance");
        $qb->addDistanceField('coordinates', $place->getLatitude(), $place->getLongitude());
        $qb->setSorting();
        $qb->addDistanceSorting('coordinates', $place->getLatitude(), $place->getLongitude(), 'asc');

        return $qb->getQuery();
    }
}
