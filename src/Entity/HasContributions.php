<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

trait HasContributions {
    public function __construct() {
        $this->contributions = new ArrayCollection();
    }

    public function addContribution(Contribution $contribution) : self {
        if ( ! $this->contributions->contains($contribution)) {
            $this->contributions[] = $contribution;
        }

        return $this;
    }

    public function removeContribution(Contribution $contribution) : void {
        $this->contributions->removeElement($contribution);
    }

    /**
     * @return Contribution[]
     */
    public function getContributions(string $sort = 'person') : array {
        $data = $this->contributions->toArray();

        $cmp = null;

        $cmp = match ($sort) {
            'year' => function (Contribution $a, Contribution $b) : int {
                $ad = $a->getPublication()->getDateYear();
                $bd = $b->getPublication()->getDateYear();

                if ( ! $ad && $bd) {
                    return -1;
                }
                if ($ad && ! $bd) {
                    return 1;
                }

                if ( ! $ad && ! $bd) {
                    return strcasecmp($a->getPublication()->getSortableTitle(), $b->getPublication()->getSortableTitle());
                }

                if ($ad->getStart(false) <=> $bd->getStart(false)) {
                    return $ad->getStart(false) <=> $bd->getStart(false);
                }

                return $a->getPublication()->getSortableTitle() <=> $b->getPublication()->getSortableTitle();
            },
            'title' => fn (Contribution $a, Contribution $b) => strcasecmp($a->getPublication()->getSortableTitle(), $b->getPublication()->getSortableTitle()),
            default => fn (Contribution $a, Contribution $b) => strcasecmp($a->getPerson()->getSortableName(), $b->getPerson()->getSortableName()),
        };

        usort($data, $cmp);

        return $data;
    }
}
