<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Description of PublicationTrait.
 *
 * @author mjoyce
 */
trait HasPublications {
    public function __construct() {
        $this->publications = new ArrayCollection();
    }

    /**
     * Add publication.
     *
     * @param Publication $publication
     *
     * @return Place
     */
    public function addPublication(Publication $publication) {
        if ( ! $this->publications->contains($publication)) {
            $this->publications[] = $publication;
        }

        return $this;
    }

    /**
     * Remove publication.
     *
     * @param Publication $publication
     */
    public function removePublication(Publication $publication) {
        $this->publications->removeElement($publication);
    }

    /**
     * Get publications.
     *
     * @param null|mixed $category
     * @param mixed $order
     *
     * @return Collection|Publication[]
     */
    public function getPublications($category = null, $order = 'title') {
        $publications = $this->publications->toArray();
        if (null !== $category) {
            $publications = array_filter($publications, function (Publication $publication) use ($category) {
                return $publication->getCategory() === $category;
            });
        }
        $cmp = null;
        switch ($order) {
            case 'title':
                $cmp = function (Publication $a, Publication $b) {
                    return strcmp($a->getSortableTitle(), $b->getSortableTitle());
                };

                break;
            case 'year':
                $cmp = function (Publication $a, Publication $b) {
                    $ad = $a->getDateYear();
                    $bd = $b->getDateYear();

                    if ( ! $ad && $bd) {
                        return -1;
                    }
                    if ($ad && ! $bd) {
                        return 1;
                    }

                    if ( ! $ad && ! $bd) {
                        return strcasecmp($a->getSortableTitle(), $b->getSortableTitle());
                    }

                    if ($ad->getStart(false) <=> $bd->getStart(false)) {
                        return $ad->getStart(false) <=> $bd->getStart(false);
                    }

                    return $a->getSortableTitle() <=> $b->getSortableTitle();
                };

                break;
        }
        usort($publications, $cmp);

        return $publications;
    }
}
