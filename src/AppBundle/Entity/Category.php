<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category extends AbstractTerm
{
    /**
     * @var Collection|Publication[]
     * @ORM\OneToMany(targetEntity="Publication", mappedBy="category")
     */
    private $publications;

    public function __construct() {
        parent::__construct();
        $this->publications = new ArrayCollection();
    }
    
    /**
     * Add publication
     *
     * @param Publication $publication
     *
     * @return Category
     */
    public function addPublication(Publication $publication)
    {
        $this->publications[] = $publication;

        return $this;
    }

    /**
     * Remove publication
     *
     * @param Publication $publication
     */
    public function removePublication(Publication $publication)
    {
        $this->publications->removeElement($publication);
    }

    /**
     * Get publications
     *
     * @return Collection
     */
    public function getPublications()
    {
        return $this->publications;
    }
}
