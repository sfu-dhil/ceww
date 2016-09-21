<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Author
 *
 * @ORM\Table(name="author")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AuthorRepository")
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
     * @ORM\Column(type="integer", nullable=true)
     * @var int
     */
    private $birthDate;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int
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
     * @ORM\Column(type="array")
     * @var array
     */
    private $original;

    /**
     * @ORM\ManyToMany(targetEntity="Alias", inversedBy="authors")
     * @ORM\JoinTable(name="author_alias");
     * @var Collection|Alias[]
     */
    private $aliases;
    
    /**
     * @ORM\ManyToMany(targetEntity="Place", inversedBy="residents")
     * @ORM\JoinTable(name="author_residence")
     * @var Collection|Place[]
     */
    private $residences;
    
    /**
     * @ORM\ManyToMany(targetEntity="Publication", inversedBy="authors")
     * @ORM\JoinTable(name="author_publication")
     * @var Collection|Publication[]
     */
    private $publications;
    
    public function __construct() {
        $this->original = array();
        $this->aliases = new ArrayCollection();
        $this->residences = new ArrayCollection();
        $this->publications = new ArrayCollection();
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
     * Set birthPlace
     *
     * @param Place $birthPlace
     *
     * @return Author
     */
    public function setBirthPlace(Place $birthPlace = null)
    {
        $this->birthPlace = $birthPlace;

        return $this;
    }

    /**
     * Get birthPlace
     *
     * @return Place
     */
    public function getBirthPlace()
    {
        return $this->birthPlace;
    }

    /**
     * Set deathPlace
     *
     * @param Place $deathPlace
     *
     * @return Author
     */
    public function setDeathPlace(Place $deathPlace = null)
    {
        $this->deathPlace = $deathPlace;

        return $this;
    }

    /**
     * Get deathPlace
     *
     * @return Place
     */
    public function getDeathPlace()
    {
        return $this->deathPlace;
    }

    /**
     * Set status
     *
     * @param Status $status
     *
     * @return Author
     */
    public function setStatus(Status $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return Status
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
     * @param Place $residence
     *
     * @return Author
     */
    public function addResidence(Place $residence)
    {
        if(! $this->residences->contains($residence)) {
            $this->residences[] = $residence;        
        }

        return $this;
    }

    /**
     * Remove residence
     *
     * @param Place $residence
     */
    public function removeResidence(Place $residence)
    {
        $this->residences->removeElement($residence);
    }

    /**
     * Get residences
     *
     * @return Collection
     */
    public function getResidences()
    {
        return $this->residences;
    }

    /**
     * Add publication
     *
     * @param Publication $publication
     *
     * @return Author
     */
    public function addPublication(Publication $publication)
    {
        if(! $this->publications->contains($publication)) {
            $this->publications[] = $publication;
        }

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
    public function getPublications($filter=null)
    {
        if($filter===null) {
            return $this->publications;
        }
        return $this->publications->filter(function(Publication $p) use($filter) {
            return $p->getPublicationType()->getLabel() === $filter;
        });
    }

    /**
     * Add alias
     *
     * @param Alias $alias
     *
     * @return Author
     */
    public function addAlias(Alias $alias)
    {
        $this->aliases[] = $alias;

        return $this;
    }

    /**
     * Remove alias
     *
     * @param Alias $alias
     */
    public function removeAlias(Alias $alias)
    {
        $this->aliases->removeElement($alias);
    }

    /**
     * Get aliases
     *
     * @return Collection
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * Set birthDate
     *
     * @param integer $birthDate
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
     * @return integer
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set deathDate
     *
     * @param integer $deathDate
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
     * @return integer
     */
    public function getDeathDate()
    {
        return $this->deathDate;
    }
    
    public function getOriginal($key = null) {
        if($key === null) {
            return $this->original;
        }
        if(array_key_exists($key, $this->original)) {
            return $this->original[$key];
        }
        return null;
    }
    
    public function setOriginal($key, $value) {
        $this->original[$key] = $value;
    }
}
