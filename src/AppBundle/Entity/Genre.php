<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    /**
     * @var Collection|Publication[]
     * @ORM\ManyToMany(targetEntity="Publication", mappedBy="genres")
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
     * @return Genre
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
    public function getPublications($category = null) {
        $publications = $this->publications->toArray();
        if($category !== null) {
            $publications = array_filter($publications, function(Publication $publication) use ($category) {
                return $publication->getCategory() === $category;
            });
        }
        usort($publications, function($a, $b) {
            return strcmp($a->getSortableTitle(), $b->getSortableTitle());
        });
        return $publications;
    }
}
