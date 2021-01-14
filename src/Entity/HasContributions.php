<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of PublicationTrait.
 *
 * @author mjoyce
 */
trait HasContributions {
    /**
     * @var ArrayCollection|Contribution[]
     * @ORM\OneToMany(targetEntity="Contribution", mappedBy="publication", cascade={"persist"}, orphanRemoval=true)
     */
    private $contributions;

    public function __construct() {
        $this->contributions = new ArrayCollection();
    }

    /**
     * Add contribution.
     *
     * @return Publication
     */
    public function addContribution(Contribution $contribution) {
        if ( ! $this->contributions->contains($contribution)) {
            $this->contributions[] = $contribution;
        }

        return $this;
    }

    /**
     * Remove contribution.
     */
    public function removeContribution(Contribution $contribution) : void {
        $this->contributions->removeElement($contribution);
    }

    /**
     * Get contributions.
     *
     * @param mixed $sort
     *
     * @return Collection
     */
    public function getContributions($sort = 'person') {
        $data = $this->contributions->toArray();

        $cmp = null;
        switch ($sort) {
            case 'year':
                $cmp = function (Contribution $a, Contribution $b) {
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
                };

                break;
            case 'title':
                $cmp = function (Contribution $a, Contribution $b) {
                    return strcasecmp($a->getPublication()->getSortableTitle(), $b->getPublication()->getSortableTitle());
                };

                break;
            case 'person':
            default:
                $cmp = function (Contribution $a, Contribution $b) {
                    return strcasecmp($a->getPerson()->getSortableName(), $b->getPerson()->getSortableName());
                };

                break;
        }

        usort($data, $cmp);

        return $data;
    }
}
