<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * Genre
 *
 * @ORM\Table(name="genre")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GenreRepository")
 */
class Genre extends AbstractTerm
{
    use HasPublications {
        HasPublications::__construct as private trait_constructor;
    }
    
    public function __construct() {
        $this->trait_constructor();
        parent::__construct();
    }
}
