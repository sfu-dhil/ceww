<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PublisherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[ORM\Table(name: 'publisher')]
#[ORM\Index(columns: ['name'], flags: ['fulltext'])]
#[ORM\Entity(repositoryClass: PublisherRepository::class)]
class Publisher extends AbstractEntity implements NormalizableInterface {
    use HasPublications {
        HasPublications::__construct as private trait_constructor;
    }

    #[ORM\Column(type: Types::STRING, length: 100, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    /**
     * @var Collection<Place>
     */
    #[ORM\ManyToMany(targetEntity: Place::class, inversedBy: 'publishers')]
    #[ORM\OrderBy(['sortableName' => 'asc'])]
    private Collection $places;

    /**
     * @var Collection<Publication>
     */
    #[ORM\ManyToMany(targetEntity: Publication::class, mappedBy: 'publishers')]
    #[ORM\OrderBy(['sortableTitle' => 'asc'])]
    private Collection $publications;

    public function __construct() {
        $this->trait_constructor();
        parent::__construct();
        $this->places = new ArrayCollection();
        $this->publications = new ArrayCollection();
    }

    public function __toString() : string {
        return $this->name;
    }

    public function setName(?string $name) {
        $this->name = $name;

        return $this;
    }

    public function getName() : ?string {
        return $this->name;
    }

    public function addPlace(Place $place) : self {
        if ( ! $this->places->contains($place)) {
            $this->places[] = $place;
        }

        return $this;
    }

    public function removePlace(Place $place) : void {
        $this->places->removeElement($place);
    }

    public function getPlaces() : Collection {
        return $this->places;
    }

    public function setPlaces(Collection $places) : void {
        $this->places = $places;
    }

    public function getNotes() : ?string {
        return $this->notes;
    }

    public function setNotes(?string $notes) : self {
        $this->notes = $notes;

        return $this;
    }

    public function getPublications() : Collection {
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

    public function normalize(NormalizerInterface $serializer, ?string $format = null, array $context = []): array
    {
        return [
            'recordType' => 'Publisher',
            'name' => $this->getName(),
            'sortable' => $this->getName(),
            'places' => array_unique(array_map(function ($place) {
                return $place->getName();
            }, $this->getPlaces()->toArray())),
        ];
    }
}
