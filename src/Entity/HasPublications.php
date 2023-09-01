<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

trait HasPublications {
    public function __construct() {
        $this->publications = new ArrayCollection();
    }

    public function addPublication(Publication $publication) : self {
        if ( ! $this->publications->contains($publication)) {
            $this->publications[] = $publication;
        }

        return $this;
    }

    public function removePublication(Publication $publication) : void {
        $this->publications->removeElement($publication);
    }

    /**
     * @return Publication[]
     */
    public function getPublications(mixed $category = null, string $order = 'title') : array {
        $publications = $this->publications->toArray();
        if (null !== $category) {
            $publications = array_filter($publications, fn (Publication $publication) => $publication->getCategory() === $category);
        }
        $cmp = null;

        switch ($order) {
            case 'title':
                $cmp = fn (Publication $a, Publication $b) => strcmp($a->getSortableTitle(), $b->getSortableTitle());

                break;

            case 'year':
                $cmp = function (Publication $a, Publication $b) : int {
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
