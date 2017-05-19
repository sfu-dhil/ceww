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
 *  @ORM\Index(columns={"name", "country_name"}, flags={"fulltext"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlaceRepository")
 */
class Place extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=250, nullable=false)
     */
    private $name;
    
    /**
     * @ORM\Column(type="array")
     * @var Collection|array
     */
    private $alternateNames;
    
    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $countryName;
    
    /**
     * @ORM\Column(type="array")
     * @var Collection|array
     */
    private $adminNames;
    
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
     */
    private $peopleBorn;
    
    /**
     * @var Collection|Person[]
     * @ORM\OneToMany(targetEntity="Person", mappedBy="deathPlace")
     */
    private $peopleDied;
    
    /**
     * @var Collection|Person[]
     * @ORM\ManyToMany(targetEntity="Person", mappedBy="residences")
     */
    private $residents;
    
    /**
     * @var Collection|Publication[]
     * @ORM\OneToMany(targetEntity="Publication", mappedBy="location")
     */
    private $publications;
    
    public function __construct() {
        parent::__construct();
        $this->alternateNames = array();
        $this->adminNames = array();
        $this->peopleBorn = new ArrayCollection();
        $this->peopleDied = new ArrayCollection();
        $this->residents = new ArrayCollection();
        $this->publications = new ArrayCollection();
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
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return preg_replace('/^[?, ]*/', '', $this->name);
    }

    /**
     * Set alternateNames
     *
     * @param array $alternateNames
     *
     * @return Place
     */
    public function setAlternateNames($alternateNames)
    {
        $this->alternateNames = $alternateNames;

        return $this;
    }

    /**
     * Get alternateNames
     *
     * @return array
     */
    public function getAlternateNames()
    {
        return $this->alternateNames;
    }

    /**
     * Set countryName
     *
     * @param string $countryName
     *
     * @return Place
     */
    public function setCountryName($countryName)
    {
        $this->countryName = $countryName;

        return $this;
    }

    /**
     * Get countryName
     *
     * @return string
     */
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * Set adminNames
     *
     * @param array $adminNames
     *
     * @return Place
     */
    public function setAdminNames($adminNames)
    {
        $this->adminNames = $adminNames;

        return $this;
    }

    /**
     * Get adminNames
     *
     * @return array
     */
    public function getAdminNames()
    {
        return $this->adminNames;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     *
     * @return Place
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     *
     * @return Place
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Place
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return Place
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Add peopleBorn
     *
     * @param Person $peopleBorn
     *
     * @return Place
     */
    public function addPersonBorn(Person $peopleBorn)
    {
        $this->peopleBorn[] = $peopleBorn;

        return $this;
    }

    /**
     * Remove peopleBorn
     *
     * @param Person $peopleBorn
     */
    public function removePersonBorn(Person $peopleBorn)
    {
        $this->peopleBorn->removeElement($peopleBorn);
    }

    /**
     * Get peopleBorn
     *
     * @return Collection
     */
    public function getPeopleBorn()
    {
        $births = $this->peopleBorn->toArray();
        usort($births, function($a, $b){
            return $a->getBirthDate()->getStart() - $b->getBirthDate()->getStart();
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
    public function addPersonDied(Person $peopleDied)
    {
        $this->peopleDied[] = $peopleDied;

        return $this;
    }

    /**
     * Remove peopleDied
     *
     * @param Person $peopleDied
     */
    public function removePersonDied(Person $peopleDied)
    {
        $this->peopleDied->removeElement($peopleDied);
    }

    /**
     * Get peopleDied
     *
     * @return Collection
     */
    public function getPeopleDied()
    {
        $deaths = $this->peopleBorn->toArray();
        usort($deaths, function($a, $b){
            return $a->getDeathDate()->getEnd() - $b->getDeathDate()->getEnd();
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
    public function addResident(Person $resident)
    {
        $this->residents[] = $resident;

        return $this;
    }

    /**
     * Remove resident
     *
     * @param Person $resident
     */
    public function removeResident(Person $resident)
    {
        $this->residents->removeElement($resident);
    }

    /**
     * Get residents
     *
     * @return Collection
     */
    public function getResidents()
    {
        $residents = $this->residents->toArray();
        usort($residents, function($a, $b) {
            return strcmp($a->getSortableName(), $b->getSortableName());
        });
        return $residents;
    }

    /**
     * Add publication
     *
     * @param Publication $publication
     *
     * @return Place
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
