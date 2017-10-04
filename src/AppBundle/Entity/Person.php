<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Person
 *
 * @ORM\Table(name="person", indexes={
 *  @ORM\Index(columns={"full_name"}, flags={"fulltext"}),
 *  @ORM\Index(columns={"sortable_name"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PersonRepository")
 */
class Person extends AbstractEntity {

    /**
     * @var string
     * @ORM\Column(type="string", length=200, nullable=false)
     */
    private $fullName;

    /**
     * @var string
     * @ORM\Column(type="string", length=200, nullable=false)
     */
    private $sortableName;

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
     * @var DateYear
     * @ORM\OneToOne(targetEntity="DateYear", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $birthDate;

    /**
     * @var Place
     * @ORM\ManyToOne(targetEntity="Place", inversedBy="peopleBorn")
     */
    private $birthPlace;

    /**
     * @var DateYear
     * @ORM\OneToOne(targetEntity="DateYear", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $deathDate;

    /**
     * @var Place;
     * @ORM\ManyToOne(targetEntity="Place", inversedBy="peopleDied")
     */
    private $deathPlace;

    /**
     * @var Collection|Place[]
     * @ORM\ManyToMany(targetEntity="Place", inversedBy="residents")
     */
    private $residences;

    /**
     * @var Collection|Place[]
     * @ORM\ManyToMany(targetEntity="Alias", inversedBy="people")
     */
    private $aliases;

    /**
     * @var Collection|Contribution[]
     * @ORM\OneToMany(targetEntity="Contribution", mappedBy="person")
     */
    private $contributions;
    
    public function __construct() {
        parent::__construct();
        $this->residences = new ArrayCollection();
        $this->aliases = new ArrayCollection();
        $this->contributions = new ArrayCollection();
    }

    public function __toString() {
        if ($this->fullName) {
            return $this->fullName;
        }
        return '(unknown)';
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     *
     * @return Person
     */
    public function setFullName($fullName) {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName() {
        if ($this->fullName) {
            return $this->fullName;
        } else {
            return '(unknown)';
        }
    }

    /**
     * Set sortableName
     *
     * @param string $sortableName
     *
     * @return Person
     */
    public function setSortableName($sortableName) {
        $this->sortableName = $sortableName;

        return $this;
    }

    /**
     * Get sortableName
     *
     * @return string
     */
    public function getSortableName() {
        return $this->sortableName;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Person
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
     * @return Person
     */
    public function setNotes($notes) {
        $this->notes = $notes;

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
     * Set birthDate
     *
     * @param string|DateYear $birthDate
     *
     * @return Person
     */
    public function setBirthDate($birthDate = null) {
        if(is_string($birthDate) || is_numeric($birthDate)) {
            $dateYear = new DateYear();
            $dateYear->setValue($birthDate);
            $this->birthDate = $dateYear;
        } else {
            $this->birthDate = $birthDate;
        }
        return $this;
    }

    /**
     * Get birthDate
     *
     * @return DateYear
     */
    public function getBirthDate() {
        return $this->birthDate;
    }

    /**
     * Set birthPlace
     *
     * @param Place $birthPlace
     *
     * @return Person
     */
    public function setBirthPlace(Place $birthPlace = null) {
        $this->birthPlace = $birthPlace;

        return $this;
    }

    /**
     * Get birthPlace
     *
     * @return Place
     */
    public function getBirthPlace() {
        return $this->birthPlace;
    }

    /**
     * Set deathDate
     *
     * @param string|DateYear $deathDate
     *
     * @return Person
     */
    public function setDeathDate($deathDate = null) {
        if(is_string($deathDate) || is_numeric($deathDate)) {
            $dateYear = new DateYear();
            $dateYear->setValue($deathDate);
            $this->deathDate = $dateYear;
        } else {
            $this->deathDate = $deathDate;
        }
    }

    /**
     * Get deathDate
     *
     * @return DateYear
     */
    public function getDeathDate() {
        return $this->deathDate;
    }

    /**
     * Set deathPlace
     *
     * @param Place $deathPlace
     *
     * @return Person
     */
    public function setDeathPlace(Place $deathPlace = null) {
        $this->deathPlace = $deathPlace;

        return $this;
    }

    /**
     * Get deathPlace
     *
     * @return Place
     */
    public function getDeathPlace() {
        return $this->deathPlace;
    }

    /**
     * Add residence
     *
     * @param Place $residence
     *
     * @return Person
     */
    public function addResidence(Place $residence) {
        if (!$this->residences->contains($residence)) {
            $this->residences[] = $residence;
        }

        return $this;
    }

    /**
     * Remove residence
     *
     * @param Place $residence
     */
    public function removeResidence(Place $residence) {
        $this->residences->removeElement($residence);
    }

    /**
     * Get residences
     *
     * @return Collection
     */
    public function getResidences() {
        return $this->residences;
    }

    /**
     * Add alias
     *
     * @param Alias $alias
     *
     * @return Person
     */
    public function addAlias(Alias $alias) {
        if (!$this->aliases->contains($alias)) {
            $this->aliases[] = $alias;
        }

        return $this;
    }

    /**
     * Remove alias
     *
     * @param Alias $alias
     */
    public function removeAlias(Alias $alias) {
        $this->aliases->removeElement($alias);
    }

    /**
     * Get aliases
     *
     * @return Collection
     */
    public function getAliases() {
        return $this->aliases;
    }

    /**
     * Add contribution
     *
     * @param Contribution $contribution
     *
     * @return Person
     */
    public function addContribution(Contribution $contribution) {
        if (!$this->contributions->contains($contribution)) {
            $this->contributions[] = $contribution;
        }

        return $this;
    }

    /**
     * Remove contribution
     *
     * @param Contribution $contribution
     */
    public function removeContribution(Contribution $contribution) {
        $this->contributions->removeElement($contribution);
    }

    /**
     * Get contributions
     *
     * @return Collection
     */
    public function getContributions($category = null) {
        if ($category === null) {
            return $this->contributions;
        }

        return array_filter($this->contributions->toArray(), function(Contribution $contribution) use ($category) {
            return $contribution->getPublication()->getCategory() === $category;
        });
    }

}
