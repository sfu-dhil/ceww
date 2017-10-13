<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * Description of PublicationTrait
 *
 * @author mjoyce
 */
trait HasPublications {

    /**
     * @var Collection|Publication[]
     * @ORM\OneToMany(targetEntity="Publication", mappedBy="location")
     */
    private $publications;
    
    public function __construct() {
        $this->publications = new ArrayCollection();
    }

    /**
     * Add publication
     *
     * @param Publication $publication
     *
     * @return Place
     */
    public function addPublication(Publication $publication) {
        if( ! $this->publications->contains($publication)) {
            $this->publications[] = $publication;
        }

        return $this;
    }

    /**
     * Remove publication
     *
     * @param Publication $publication
     */
    public function removePublication(Publication $publication) {
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
