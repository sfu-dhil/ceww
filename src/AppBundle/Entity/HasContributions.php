<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Description of PublicationTrait
 *
 * @author mjoyce
 */
trait HasContributions {

    public function __construct() {
        $this->contributions = new ArrayCollection();
    }

    /**
     * Add contribution
     *
     * @param Contribution $contribution
     *
     * @return Publication
     */
    public function addContribution(Contribution $contribution) {
        if (!$this->contributions->contains($contribution)) {
            $this->contributions[] = $contribution;
        }

        return $this;
    }

    /**
     * Remove contribution
     *
     * @param Contribution $contribution
     */
    public function removeContribution(Contribution $contribution) {
        $this->contributions->removeElement($contribution);
    }

    /**
     * Get contributions
     *
     * @return Collection
     */
    public function getContributions($sort = 'person') {
        $data = $this->contributions->toArray();

        $cmp = null;
        switch($sort) {
            case 'year':
                $cmp = function(Contribution $a, Contribution $b) {
                    $ad = $a->getPublication()->getDateYear();
                    $bd = $b->getPublication()->getDateYear();

                    if( ! $ad && $bd) {
                        return -1;
                    }
                    if( $ad && !$bd) {
                        return 1;
                    }

                    if( ! $ad && ! $bd ) {
                        return strcasecmp($a->getPublication()->getSortableTitle(), $b->getPublication()->getSortableTitle());
                    }

                    if($ad->getStart(false) <=> $bd->getStart(false)) {
                        return $ad->getStart(false) <=> $bd->getStart(false);
                    }

                    return $a->getPublication()->getSortableTitle() <=> $b->getPublication()->getSortableTitle();
                };
                break;
            case 'title':
                $cmp = function(Contribution $a, Contribution $b) {
                    return strcasecmp($a->getPublication()->getSortableTitle(), $b->getPublication()->getSortableTitle());
                };
                break;
            case 'person':
            default:
                $cmp = function(Contribution $a, Contribution $b) {
                    return strcasecmp($a->getPerson()->getSortableName(), $b->getPerson()->getSortableName());
                };
                break;
        }

        usort($data, $cmp);
        return $data;
    }

}
