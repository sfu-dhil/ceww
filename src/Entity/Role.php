<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

#[ORM\Table(name: 'role')]
#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role extends AbstractTerm {
    /**
     * @var Collection<Contribution>
     */
    #[ORM\OneToMany(targetEntity: Contribution::class, mappedBy: 'role')]
    private Collection $contributions;

    public function __construct() {
        parent::__construct();
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

    public function getContributions() : array {
        $contributions = $this->contributions->toArray();
        usort($contributions, fn (Contribution $a, Contribution $b) => strcasecmp($a->getPerson()->getSortableName(), $b->getPerson()->getSortableName()));

        return $contributions;
    }
}
