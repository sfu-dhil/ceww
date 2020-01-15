<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * Genre.
 *
 * @ORM\Table(name="genre")
 * @ORM\Entity(repositoryClass="App\Repository\GenreRepository")
 */
class Genre extends AbstractTerm {
    use HasPublications {
        HasPublications::__construct as private trait_constructor;
    }

    /**
     * @var Collection|Publication[]
     * @ORM\ManyToMany(targetEntity="Publication", mappedBy="genres")
     * @ORM\OrderBy({"title" = "ASC"})
     */
    private $publications;

    public function __construct() {
        $this->trait_constructor();
        parent::__construct();
    }
}