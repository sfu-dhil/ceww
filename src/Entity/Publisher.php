<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\SolrBundle\Annotation as Solr;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Publisher.
 *
 * @ORM\Table(name="publisher", indexes={
 *     @ORM\Index(columns={"name"}, flags={"fulltext"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\PublisherRepository")
 *
 * @Solr\Document(
 *     @Solr\CopyField(from={"name", "places"}, to="content", type="texts"),
 *     @Solr\CopyField(from={"name"}, to="sortable", type="string"),
 *     @Solr\CopyField(from={"places"}, to="places_fct", type="strings")
 * )
 */
class Publisher extends AbstractEntity {
    use HasPublications {
        HasPublications::__construct as private trait_constructor;

    }

    /**
     * @var string
     * @ORM\Column(type="string", length=100, nullable=false)
     * @ORM\OrderBy({"sortableName": "ASC"})
     *
     * @Solr\Field(type="text", boost=2.0)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @var Collection|Place[]
     * @ORM\ManyToMany(targetEntity="Place", inversedBy="publishers")
     * @ORM\OrderBy({"sortableName": "ASC"})
     *
     * @Solr\Field(type="texts", boost=0.6, getter="getPlaces(true)")
     */
    private $places;

    /**
     * @var Collection|Publication[]
     * @ORM\ManyToMany(targetEntity="Publication", mappedBy="publishers")
     * @ORM\OrderBy({"sortableTitle": "ASC"})
     */
    private $publications;

    public function __construct() {
        $this->trait_constructor();
        parent::__construct();
        $this->places = new ArrayCollection();
        $this->publications = new ArrayCollection();
    }

    public function __toString() : string {
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
     */
    public function removePlace(Place $place) : void {
        $this->places->removeElement($place);
    }

    /**
     * Get places.
     *
     * @param ?bool $flat
     *
     * @return Collection
     */
    public function getPlaces(?bool $flat = false) {
        if ($flat) {
            return array_map(fn (Place $p) => $p->getName(), $this->places->toArray());
        }

        return $this->places;
    }

    public function setPlaces($places) : void {
        $this->places = $places;
    }

    public function getNotes() : ?string {
        return $this->notes;
    }

    public function setNotes(?string $notes) : self {
        $this->notes = $notes;

        return $this;
    }

    /**
     * @param ?bool $flat
     *
     * @return mixed
     */
    public function getPublications(?bool $flat = false) {
        if ($flat) {
            return array_map(fn (Publication $p) => $p->getTitle(), $this->publications->toArray());
        }

        return $this->publications;
    }

    public function addPublication(Publication $publication) : self {
        if ( ! $this->publications->contains($publication)) {
            $this->publications[] = $publication;
            $publication->addPublisher($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication) : self {
        if ($this->publications->contains($publication)) {
            $this->publications->removeElement($publication);
            $publication->removePublisher($this);
        }

        return $this;
    }
}
