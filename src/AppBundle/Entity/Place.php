<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Place
 *
 * @ORM\Table(name="place", indexes={
 *  @ORM\Index(columns={"name", "country_name"}, flags={"fulltext"}),
 *  @ORM\Index(columns={"sortable_name"}, flags={"fulltext"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlaceRepository")
 */
class Place extends AbstractEntity {

    use HasPublications {
        HasPublications::__construct as private trait_constructor;
    }

    /**
     * @ORM\Column(type="string", length=250, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=250, nullable=false)
     */
    private $sortableName;

    /**
     * @var string
     * @ORM\Column(name="geonames_id", type="string", length=16, nullable=true)
     */
    private $geoNamesId;

    /**
     * A province, state, territory or other sub-national entity.
     *
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $regionName;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $countryName;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=6, nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="decimal", precision=9, scale=6, nullable=true)
     */
    private $longitude;

    /**
     * public research notes.
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * private research notes.
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @var Collection|Person[]
     * @ORM\OneToMany(targetEntity="Person", mappedBy="birthPlace")
     * @ORM\OrderBy({"sortableName" = "ASC"})
     */
    private $peopleBorn;

    /**
     * @var Collection|Person[]
     * @ORM\OneToMany(targetEntity="Person", mappedBy="deathPlace")
     * @ORM\OrderBy({"sortableName" = "ASC"})
     */
    private $peopleDied;

    /**
     * @var Collection|Person[]
     * @ORM\ManyToMany(targetEntity="Person", mappedBy="residences")
     * @ORM\OrderBy({"sortableName" = "ASC"})
     */
    private $residents;

    /**
     * @var Collection|Publication[]
     * @ORM\OneToMany(targetEntity="Publication", mappedBy="location")
     * @ORM\OrderBy({"title" = "ASC"})
     */
    private $publications;

    /**
     * @var Collection|Publisher[]
     * @ORM\ManyToMany(targetEntity="Publisher", mappedBy="places")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $publishers;

    public function __construct() {
        $this->trait_constructor();
        parent::__construct();
        $this->peopleBorn = new ArrayCollection();
        $this->peopleDied = new ArrayCollection();
        $this->residents = new ArrayCollection();
        $this->publishers = new ArrayCollection();
        $this->notes = '';
    }

    public function __toString() {
        return preg_replace('/^[?, ]*/', '', $this->name);
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Place
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return preg_replace('/^[?, ]*/', '', $this->name);
    }

    /**
     * Set countryName
     *
     * @param string $countryName
     *
     * @return Place
     */
    public function setCountryName($countryName) {
        $this->countryName = $countryName;

        return $this;
    }

    /**
     * Get countryName
     *
     * @return string
     */
    public function getCountryName() {
        return $this->countryName;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     *
     * @return Place
     */
    public function setLatitude($latitude) {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude() {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     *
     * @return Place
     */
    public function setLongitude($longitude) {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude() {
        return $this->longitude;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Place
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return Place
     */
    public function setNotes($notes) {
        $this->notes = $notes;

        return $this;
    }

    public function appendNote($note) {
        if (!$this->notes) {
            $this->notes = $note;
        } else {
            $this->notes .= "\n\n" . $note;
        }
        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes() {
        return $this->notes;
    }

    /**
     * Add peopleBorn
     *
     * @param Person $peopleBorn
     *
     * @return Place
     */
    public function addPersonBorn(Person $peopleBorn) {
        if (!$this->peopleBorn->contains($peopleBorn)) {
            $this->peopleBorn[] = $peopleBorn;
        }

        return $this;
    }

    /**
     * Remove peopleBorn
     *
     * @param Person $peopleBorn
     */
    public function removePersonBorn(Person $peopleBorn) {
        $this->peopleBorn->removeElement($peopleBorn);
    }

    /**
     * Get peopleBorn
     *
     * @return Collection
     */
    public function getPeopleBorn() {
        $births = $this->peopleBorn->toArray();
        usort($births, function($a, $b) {
            $aDate = $a->getBirthDate();
            $bDate = $b->getBirthDate();
            if( (! $aDate) && (!$bDate)) {
                return 0;
            }
            if( (! $aDate) && $bDate) {
                return -1;
            }
            if( $aDate && (! $bDate)) {
                return 1;
            }
            return $aDate->getStart(false) - $bDate->getStart(false);
        });
        return $births;
    }

    /**
     * Add peopleDied
     *
     * @param Person $peopleDied
     *
     * @return Place
     */
    public function addPersonDied(Person $peopleDied) {
        if (!$this->peopleDied->contains($peopleDied)) {
            $this->peopleDied[] = $peopleDied;
        }

        return $this;
    }

    /**
     * Remove peopleDied
     *
     * @param Person $peopleDied
     */
    public function removePersonDied(Person $peopleDied) {
        $this->peopleDied->removeElement($peopleDied);
    }

    /**
     * Get peopleDied
     *
     * @return Collection
     */
    public function getPeopleDied() {
        $deaths = $this->peopleDied->toArray();
        usort($deaths, function($a, $b) {
            $aDate = $a->getBirthDate();
            $bDate = $b->getBirthDate();
            if( (! $aDate) && (!$bDate)) {
                return 0;
            }
            if( (! $aDate) && $bDate) {
                return -1;
            }
            if( $aDate && (! $bDate)) {
                return 1;
            }
            return $aDate->getStart(false) - $bDate->getStart(false);
        });
        return $deaths;
    }

    /**
     * Add resident
     *
     * @param Person $resident
     *
     * @return Place
     */
    public function addResident(Person $resident) {
        if( ! $this->residents->contains($resident)) {
            $this->residents[] = $resident;
        }

        return $this;
    }

    /**
     * Remove resident
     *
     * @param Person $resident
     */
    public function removeResident(Person $resident) {
        $this->residents->removeElement($resident);
    }

    /**
     * Get residents
     *
     * @return Collection
     */
    public function getResidents() {
        $residents = $this->residents->toArray();
        usort($residents, function($a, $b) {
            return strcmp($a->getSortableName(), $b->getSortableName());
        });
        return $residents;
    }


    /**
     * Set sortableName
     *
     * @param string $sortableName
     *
     * @return Place
     */
    public function setSortableName($sortableName)
    {
        $this->sortableName = $sortableName;

        return $this;
    }

    /**
     * Get sortableName
     *
     * @return string
     */
    public function getSortableName()
    {
        return $this->sortableName;
    }

    /**
     * Add peopleBorn
     *
     * @param Person $peopleBorn
     *
     * @return Place
     */
    public function addPeopleBorn(Person $peopleBorn)
    {
        $this->peopleBorn[] = $peopleBorn;

        return $this;
    }

    /**
     * Remove peopleBorn
     *
     * @param Person $peopleBorn
     */
    public function removePeopleBorn(Person $peopleBorn)
    {
        $this->peopleBorn->removeElement($peopleBorn);
    }

    /**
     * Add peopleDied
     *
     * @param Person $peopleDied
     *
     * @return Place
     */
    public function addPeopleDied(Person $peopleDied)
    {
        $this->peopleDied[] = $peopleDied;

        return $this;
    }

    /**
     * Remove peopleDied
     *
     * @param Person $peopleDied
     */
    public function removePeopleDied(Person $peopleDied)
    {
        $this->peopleDied->removeElement($peopleDied);
    }

    /**
     * Set regionName
     *
     * @param string $regionName
     *
     * @return Place
     */
    public function setRegionName($regionName)
    {
        $this->regionName = $regionName;

        return $this;
    }

    /**
     * Get regionName
     *
     * @return string
     */
    public function getRegionName()
    {
        return $this->regionName;
    }

    /**
     * Add publisher
     *
     * @param Publisher $publisher
     *
     * @return Place
     */
    public function addPublisher(Publisher $publisher)
    {
        $this->publishers[] = $publisher;

        return $this;
    }

    /**
     * Remove publisher
     *
     * @param Publisher $publisher
     */
    public function removePublisher(Publisher $publisher)
    {
        $this->publishers->removeElement($publisher);
    }

    /**
     * Get publishers
     *
     * @return Collection
     */
    public function getPublishers()
    {
        return $this->publishers;
    }

    /**
     * Set geoNamesId.
     *
     * @param string|null $geoNamesId
     *
     * @return Place
     */
    public function setGeoNamesId($geoNamesId = null)
    {
        $this->geoNamesId = $geoNamesId;

        return $this;
    }

    /**
     * Get geoNamesId.
     *
     * @return string|null
     */
    public function getGeoNamesId()
    {
        return $this->geoNamesId;
    }
}
