<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Publisher.
 *
 * @ORM\Table(name="publisher", indexes={
 *  @ORM\Index(columns="name", flags={"fulltext"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\PublisherRepository")
 */
class Publisher extends AbstractEntity {
    use HasPublications {
        HasPublications::__construct as private trait_constructor;
    }

    /**
     * @var string
     * @ORM\Column(type="string", length=100, nullable=false)
     * @ORM\OrderBy({"sortableName" = "ASC"})
     */
    private $name;

    /**
     * @var Collection|Place[]
     * @ORM\ManyToMany(targetEntity="Place", inversedBy="publishers")
     * @ORM\OrderBy({"sortableName" = "ASC"})
     */
    private $places;

    /**
     * @var Collection|Publication[]
     * @ORM\ManyToMany(targetEntity="Publication", mappedBy="publishers")
     * @ORM\OrderBy({"sortableTitle" = "ASC"})
     */
    private $publications;

    public function __construct() {
        $this->trait_constructor();
        parent::__construct();
        $this->places = new ArrayCollection();
    }

    public function __toString() {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Publisher
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Add place.
     *
     * @param Place $place
     *
     * @return Publisher
     */
    public function addPlace(Place $place) {
        if ( ! $this->places->contains($place)) {
            $this->places[] = $place;
        }

        return $this;
    }

    /**
     * Remove place.
     *
     * @param Place $place
     */
    public function removePlace(Place $place) {
        $this->places->removeElement($place);
    }

    /**
     * Get places.
     *
     * @return Collection
     */
    public function getPlaces() {
        return $this->places;
    }

    public function setPlaces($places) {
        $this->places = $places;
    }
}
