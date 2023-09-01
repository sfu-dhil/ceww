<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

#[ORM\Table(name: 'genre')]
#[ORM\Entity(repositoryClass: GenreRepository::class)]
class Genre extends AbstractTerm {
    use HasPublications {
        HasPublications::__construct as private trait_constructor;
    }

    /**
     * @var Collection<Publication>
     */
    #[ORM\ManyToMany(targetEntity: Publication::class, mappedBy: 'genres')]
    #[ORM\OrderBy(['title' => 'asc'])]
    private Collection $publications;

    public function __construct() {
        $this->publications = new ArrayCollection();
        $this->trait_constructor();
        parent::__construct();
    }
}
