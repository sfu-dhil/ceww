<?php

namespace AppBundle\Entity;

use AppBundle\Repository\AuthorRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Author
 *
 * @ORM\Table(name="author")
 * @ORM\Entity(repositoryClass="AuthorRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Author extends AbstractEntity{

    /**
     * @ORM\Column(type="string", length=120, nullable=false)
     */
    private $fullName;
    
    /**
     * @ORM\Column(type="string", length=120, nullable=false)
     */
    private $sortableName;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @var DateTime
     */
    private $birthDate;
    
    /**
     * @ORM\Column(type="date", nullable=true)
     * @var DateTime
     */
    private $deathDate;
    
    /**
     * @ORM\ManyToOne(targetEntity="Place", inversedBy="authorsBorn")
     * @ORM\JoinColumn(name="birthplace_id", referencedColumnName="id", nullable=true)
     * @var Place 
     */
    private $birthPlace;
    
    /**
     * @ORM\ManyToOne(targetEntity="Place", inversedBy="authorsDied")
     * @ORM\JoinColumn(name="deathplace_id", referencedColumnName="id", nullable=true)
     * @var Place
     */
    private $deathPlace;
    
    /**
     * @ORM\ManyToOne(targetEntity="Status", inversedBy="publishedAuthors")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", nullable=false)
     * @var Status 
     */
    private $status;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @ORM\ManyToMany(targetEntity="Place", inversedBy="residents")
     * @ORM\JoinTable(name="author_residence")
     * @var Collection|Place[]
     */
    private $residences;
    
    public function __construct() {
        $this->residences = new ArrayCollection();
    }
    
    public function __toString() {
        return $this->fullName;
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     *
     * @return Author
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set sortableName
     *
     * @param string $sortableName
     *
     * @return Author
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
        if($this->sortableName === null) {
            return $this->fullName;
        }
        return $this->sortableName;
    }
    
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateSortableName() {
        if($this->sortableName === null || $this->sortableName === '') {
            $this->sortableName = $this->fullName;
        }
    }
    
    /**
     * Set birthDate
     *
     * @param DateTime$birthDate
     *
     * @return Author
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return DateTime     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set deathDate
     *
     * @param DateTime$deathDate
     *
     * @return Author
     */
    public function setDeathDate($deathDate)
    {
        $this->deathDate = $deathDate;

        return $this;
    }

    /**
     * Get deathDate
     *
     * @return DateTime     */
    public function getDeathDate()
    {
        return $this->deathDate;
    }

    /**
     * Set birthPlace
     *
     * @param \AppBundle\Entity\Place $birthPlace
     *
     * @return Author
     */
    public function setBirthPlace(\AppBundle\Entity\Place $birthPlace = null)
    {
        $this->birthPlace = $birthPlace;

        return $this;
    }

    /**
     * Get birthPlace
     *
     * @return \AppBundle\Entity\Place
     */
    public function getBirthPlace()
    {
        return $this->birthPlace;
    }

    /**
     * Set deathPlace
     *
     * @param \AppBundle\Entity\Place $deathPlace
     *
     * @return Author
     */
    public function setDeathPlace(\AppBundle\Entity\Place $deathPlace = null)
    {
        $this->deathPlace = $deathPlace;

        return $this;
    }

    /**
     * Get deathPlace
     *
     * @return \AppBundle\Entity\Place
     */
    public function getDeathPlace()
    {
        return $this->deathPlace;
    }

    /**
     * Set status
     *
     * @param \AppBundle\Entity\Status $status
     *
     * @return Author
     */
    public function setStatus(\AppBundle\Entity\Status $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \AppBundle\Entity\Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return Author
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
     * Add residence
     *
     * @param \AppBundle\Entity\Place $residence
     *
     * @return Author
     */
    public function addResidence(\AppBundle\Entity\Place $residence)
    {
        $this->residences[] = $residence;

        return $this;
    }

    /**
     * Remove residence
     *
     * @param \AppBundle\Entity\Place $residence
     */
    public function removeResidence(\AppBundle\Entity\Place $residence)
    {
        $this->residences->removeElement($residence);
    }

    /**
     * Get residences
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResidences()
    {
        return $this->residences;
    }
}
