<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * Place
 *
 * @ORM\Table(name="place")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlaceRepository")
 * @UniqueEntity("name")
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;
    
    /**
     * @ORM\OneToMany(targetEntity="Author", mappedBy="birthPlace")
     * @var Collection|Author[]
     */
    private $authorsBorn;
    
    /**
     * @ORM\OneToMany(targetEntity="Author", mappedBy="deathPlace")
     * @var Collection|Author[]
     */
    private $authorsDied;
    
    /**
     * @ORM\ManyToMany(targetEntity="Author", mappedBy="residences")
     * @ORM\JoinTable(name="author_residence")
     * @var Collection|Author[]
     */
    private $residents;

    public function __construct() {
        $this->alternateNames = array();
        $this->adminNames = array();
        $this->authorsBorn = new ArrayCollection();
        $this->authorsDied = new ArrayCollection();
        $this->residents = new ArrayCollection();
    }
    
    public function __toString() {
        return $this->name;
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
        return $this->name;
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
     * Add authorsBorn
     *
     * @param \AppBundle\Entity\Author $authorsBorn
     *
     * @return Place
     */
    public function addAuthorsBorn(\AppBundle\Entity\Author $authorsBorn)
    {
        $this->authorsBorn[] = $authorsBorn;

        return $this;
    }

    /**
     * Remove authorsBorn
     *
     * @param \AppBundle\Entity\Author $authorsBorn
     */
    public function removeAuthorsBorn(\AppBundle\Entity\Author $authorsBorn)
    {
        $this->authorsBorn->removeElement($authorsBorn);
    }

    /**
     * Get authorsBorn
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuthorsBorn()
    {
        return $this->authorsBorn;
    }

    /**
     * Add authorsDied
     *
     * @param \AppBundle\Entity\Author $authorsDied
     *
     * @return Place
     */
    public function addAuthorsDied(\AppBundle\Entity\Author $authorsDied)
    {
        $this->authorsDied[] = $authorsDied;

        return $this;
    }

    /**
     * Remove authorsDied
     *
     * @param \AppBundle\Entity\Author $authorsDied
     */
    public function removeAuthorsDied(\AppBundle\Entity\Author $authorsDied)
    {
        $this->authorsDied->removeElement($authorsDied);
    }

    /**
     * Get authorsDied
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuthorsDied()
    {
        return $this->authorsDied;
    }

    /**
     * Add resident
     *
     * @param \AppBundle\Entity\Author $resident
     *
     * @return Place
     */
    public function addResident(\AppBundle\Entity\Author $resident)
    {
        $this->residents[] = $resident;

        return $this;
    }

    /**
     * Remove resident
     *
     * @param \AppBundle\Entity\Author $resident
     */
    public function removeResident(\AppBundle\Entity\Author $resident)
    {
        $this->residents->removeElement($resident);
    }

    /**
     * Get residents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResidents()
    {
        return $this->residents;
    }
}
