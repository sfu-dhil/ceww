<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PlaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[ORM\Table(name: 'place')]
#[ORM\Index(columns: ['name', 'country_name'], flags: ['fulltext'])]
#[ORM\Index(columns: ['sortable_name'], flags: ['fulltext'])]
#[ORM\Entity(repositoryClass: PlaceRepository::class)]
class Place extends AbstractEntity implements NormalizableInterface {
    use HasPublications {
        HasPublications::__construct as private trait_constructor;
    }

    #[ORM\Column(type: Types::STRING, length: 250, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 250, nullable: false)]
    private ?string $sortableName = null;

    #[ORM\Column(name: 'geonames_id', type: Types::STRING, length: 16, nullable: true)]
    private ?string $geoNamesId = null;

    #[ORM\Column(type: Types::STRING, length: 250, nullable: true)]
    private ?string $regionName = null;

    #[ORM\Column(type: Types::STRING, length: 250, nullable: true)]
    private ?string $countryName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 6, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 9, scale: 6, nullable: true)]
    private ?string $longitude = null;

    /**
     * public research notes.
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * private research notes.
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    /**
     * @var Collection<Person>
     */
    #[ORM\OneToMany(targetEntity: Person::class, mappedBy: 'birthPlace')]
    #[ORM\OrderBy(['sortableName' => 'asc'])]
    private Collection $peopleBorn;

    /**
     * @var Collection<Person>
     */
    #[ORM\OneToMany(targetEntity: Person::class, mappedBy: 'deathPlace')]
    #[ORM\OrderBy(['sortableName' => 'asc'])]
    private Collection $peopleDied;

    /**
     * @var Collection<Person>
     */
    #[ORM\ManyToMany(targetEntity: Person::class, mappedBy: 'residences')]
    #[ORM\OrderBy(['sortableName' => 'asc'])]
    private Collection $residents;

    /**
     * @var Collection<Publication>
     */
    #[ORM\OneToMany(targetEntity: Publication::class, mappedBy: 'location')]
    #[ORM\OrderBy(['title' => 'asc'])]
    private Collection $publications;

    /**
     * @var Collection<Publisher>
     */
    #[ORM\ManyToMany(targetEntity: Publisher::class, mappedBy: 'places')]
    #[ORM\OrderBy(['name' => 'asc'])]
    private Collection $publishers;

    public function __construct() {
        $this->publications = new ArrayCollection();
        $this->trait_constructor();
        parent::__construct();
        $this->peopleBorn = new ArrayCollection();
        $this->peopleDied = new ArrayCollection();
        $this->residents = new ArrayCollection();
        $this->publishers = new ArrayCollection();
        $this->notes = '';
    }

    public function __toString() : string {
        return preg_replace('/^[?, ]*/', '', $this->name);
    }

    public function setName(?string $name) : self {
        $this->name = $name;

        return $this;
    }

    public function getName() : ?string {
        if (null === $this->name) {
            return $this->name;
        }

        return preg_replace('/^[?, ]*/', '', $this->name);
    }

    public function setCountryName(?string $countryName) : self {
        $this->countryName = $countryName;

        return $this;
    }

    public function getCountryName() : ?string {
        return $this->countryName;
    }

    public function setLatitude(?string $latitude) : self {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLatitude() : ?string {
        return $this->latitude;
    }

    public function setLongitude(?string $longitude) : self {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLongitude() : ?string {
        return $this->longitude;
    }

    public function setDescription(?string $description) : self {
        $this->description = $description;

        return $this;
    }

    public function getDescription() : ?string {
        return $this->description;
    }

    public function getDescriptionSanitized() : ?string {
        return strip_tags(html_entity_decode($this->description ?? ''));
    }

    public function setNotes(?string $notes) : self {
        $this->notes = $notes;

        return $this;
    }

    public function appendNote(?string $note) : self {
        if ( ! $this->notes) {
            $this->notes = $note;
        } else {
            $this->notes .= "\n\n" . $note;
        }

        return $this;
    }

    public function getNotes() : ?string {
        return $this->notes;
    }

    public function addPersonBorn(Person $peopleBorn) : self {
        if ( ! $this->peopleBorn->contains($peopleBorn)) {
            $this->peopleBorn[] = $peopleBorn;
        }

        return $this;
    }

    public function removePersonBorn(Person $peopleBorn) : void {
        $this->peopleBorn->removeElement($peopleBorn);
    }

    /**
     * @var Person[]
     */
    public function getPeopleBorn() : array {
        $births = $this->peopleBorn->toArray();
        usort($births, function ($a, $b) {
            $aDate = $a->getBirthDate();
            $bDate = $b->getBirthDate();
            if (( ! $aDate) && ( ! $bDate)) {
                return 0;
            }
            if (( ! $aDate) && $bDate) {
                return -1;
            }
            if ($aDate && ( ! $bDate)) {
                return 1;
            }

            return $aDate->getStart(false) - $bDate->getStart(false);
        });

        return $births;
    }

    public function addPersonDied(Person $peopleDied) : self {
        if ( ! $this->peopleDied->contains($peopleDied)) {
            $this->peopleDied[] = $peopleDied;
        }

        return $this;
    }

    public function removePersonDied(Person $peopleDied) : void {
        $this->peopleDied->removeElement($peopleDied);
    }

    /**
     * @var Person[]
     */
    public function getPeopleDied() : array {
        $deaths = $this->peopleDied->toArray();
        usort($deaths, function ($a, $b) {
            $aDate = $a->getDeathDate();
            $bDate = $b->getDeathDate();
            if (( ! $aDate) && ( ! $bDate)) {
                return 0;
            }
            if (( ! $aDate) && $bDate) {
                return -1;
            }
            if ($aDate && ( ! $bDate)) {
                return 1;
            }

            return $aDate->getStart(false) - $bDate->getStart(false);
        });

        return $deaths;
    }

    public function addResident(Person $resident) : self {
        if ( ! $this->residents->contains($resident)) {
            $this->residents[] = $resident;
        }

        return $this;
    }

    public function removeResident(Person $resident) : void {
        $this->residents->removeElement($resident);
    }

    /**
     * @var Person[]
     */
    public function getResidents() : array {
        $residents = $this->residents->toArray();
        usort($residents, fn ($a, $b) => strcmp((string) $a->getSortableName(), (string) $b->getSortableName()));

        return $residents;
    }

    public function setSortableName(?string $sortableName) : self {
        $this->sortableName = $sortableName;

        return $this;
    }

    public function getSortableName() : ?string {
        return $this->sortableName;
    }

    public function addPeopleBorn(Person $peopleBorn) : self {
        $this->peopleBorn[] = $peopleBorn;

        return $this;
    }

    public function removePeopleBorn(Person $peopleBorn) : void {
        $this->peopleBorn->removeElement($peopleBorn);
    }

    public function addPeopleDied(Person $peopleDied) : self {
        $this->peopleDied[] = $peopleDied;

        return $this;
    }

    public function removePeopleDied(Person $peopleDied) : void {
        $this->peopleDied->removeElement($peopleDied);
    }

    public function setRegionName(?string $regionName) : self {
        $this->regionName = $regionName;

        return $this;
    }

    public function getRegionName() : ?string {
        return $this->regionName;
    }

    public function addPublisher(Publisher $publisher) : self {
        $this->publishers[] = $publisher;

        return $this;
    }

    public function removePublisher(Publisher $publisher) : void {
        $this->publishers->removeElement($publisher);
    }

    /**
     * @return Collection|Publisher[]
     */
    public function getPublishers() : Collection {
        return $this->publishers;
    }

    public function setGeoNamesId(?string $geoNamesId = null) : self {
        $this->geoNamesId = $geoNamesId;

        return $this;
    }

    public function getGeoNamesId() : ?string {
        return $this->geoNamesId;
    }

    public function getCoordinates() : ?string {
        if ($this->latitude && $this->longitude) {
            return $this->latitude . ',' . $this->longitude;
        }

        return null;
    }

    public function normalize(NormalizerInterface $serializer, ?string $format = null, array $context = []): array
    {
        $data = [
            'recordType' => 'Place',
            'name' => $this->getName(),
            'sortable' => $this->getSortableName(),
            'region' => $this->getRegionName(),
            'country' => $this->getCountryName(),
            'description' => $this->getDescriptionSanitized(),
        ];
        if ($this->getCoordinates()) {
            $data['_geo'] = [
                'lat' => $this->getLatitude(),
                'lng' => $this->getLongitude(),
            ];
        }
        return $data;
    }
}
