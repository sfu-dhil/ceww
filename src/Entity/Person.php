<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\MediaBundle\Entity\LinkableInterface;
use Nines\MediaBundle\Entity\LinkableTrait;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Person.
 *
 * @ORM\Table(name="person", indexes={
 *     @ORM\Index(columns={"full_name"}, flags={"fulltext"}),
 *     @ORM\Index(columns={"sortable_name"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\PersonRepository")
 */
class Person extends AbstractEntity implements LinkableInterface {
    use HasContributions {
        HasContributions::__construct as private trait_constructor;
        getContributions as private traitContributions;
    }

    use LinkableTrait {
        LinkableTrait::__construct as private link_constructor;
    }

    public const MALE = 'm';

    public const FEMALE = 'f';

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
     * @var string
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $gender;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true, options={"default": true})
     */
    private $canadian;

    /**
     * public research notes.
     *
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * private research notes.
     *
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @var string[]
     * @ORM\Column(type="array")
     */
    private $urlLinks;

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
     * @ORM\OrderBy({"sortableName": "ASC"})
     */
    private $residences;

    /**
     * @var Collection|Place[]
     * @ORM\ManyToMany(targetEntity="Alias", inversedBy="people")
     * @ORM\OrderBy({"sortableName": "ASC"})
     */
    private $aliases;

    /**
     * @var Collection|Contribution[]
     * @ORM\OneToMany(targetEntity="Contribution", mappedBy="person", orphanRemoval=true)
     */
    private $contributions;

    public function __construct() {
        parent::__construct();
        $this->trait_constructor();
        $this->link_constructor();
        $this->canadian = true;
        $this->residences = new ArrayCollection();
        $this->aliases = new ArrayCollection();
        $this->urlLinks = [];
    }

    public function __toString() : string {
        if ($this->fullName) {
            return $this->fullName;
        }

        return '(unknown)';
    }

    /**
     * Set fullName.
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
     * Get fullName.
     *
     * @return string
     */
    public function getFullName() {
        if ($this->fullName) {
            return $this->fullName;
        }

        return '(unknown)';
    }

    /**
     * Set sortableName.
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
     * Get sortableName.
     *
     * @return string
     */
    public function getSortableName() {
        return $this->sortableName;
    }

    /**
     * Set description.
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
     * Get description.
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set notes.
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
     * Get notes.
     *
     * @return string
     */
    public function getNotes() {
        return $this->notes;
    }

    /**
     * Set birthDate.
     *
     * @param DateYear|string $birthDate
     *
     * @return Person
     */
    public function setBirthDate($birthDate = null) {
        if (is_string($birthDate) || is_numeric($birthDate)) {
            $dateYear = new DateYear();
            $dateYear->setValue($birthDate);
            $this->birthDate = $dateYear;
        } else {
            $this->birthDate = $birthDate;
        }

        return $this;
    }

    /**
     * Get birthDate.
     *
     * @return DateYear
     */
    public function getBirthDate() {
        return $this->birthDate;
    }

    /**
     * Set birthPlace.
     *
     * @param Place $birthPlace
     *
     * @return Person
     */
    public function setBirthPlace(?Place $birthPlace = null) {
        $this->birthPlace = $birthPlace;

        return $this;
    }

    /**
     * Get birthPlace.
     *
     * @return Place
     */
    public function getBirthPlace() {
        return $this->birthPlace;
    }

    /**
     * Set deathDate.
     *
     * @param DateYear|string $deathDate
     *
     * @return Person
     */
    public function setDeathDate($deathDate = null) {
        if (is_string($deathDate) || is_numeric($deathDate)) {
            $dateYear = new DateYear();
            $dateYear->setValue($deathDate);
            $this->deathDate = $dateYear;
        } else {
            $this->deathDate = $deathDate;
        }
    }

    /**
     * Get deathDate.
     *
     * @return DateYear
     */
    public function getDeathDate() {
        return $this->deathDate;
    }

    /**
     * Set deathPlace.
     *
     * @param Place $deathPlace
     *
     * @return Person
     */
    public function setDeathPlace(?Place $deathPlace = null) {
        $this->deathPlace = $deathPlace;

        return $this;
    }

    /**
     * Get deathPlace.
     *
     * @return Place
     */
    public function getDeathPlace() {
        return $this->deathPlace;
    }

    /**
     * Add residence.
     *
     * @return Person
     */
    public function addResidence(Place $residence) {
        if ( ! $this->residences->contains($residence)) {
            $this->residences[] = $residence;
        }

        return $this;
    }

    /**
     * Remove residence.
     */
    public function removeResidence(Place $residence) : void {
        $this->residences->removeElement($residence);
    }

    /**
     * Get residences.
     *
     * @return Collection
     */
    public function getResidences() {
        return $this->residences;
    }

    /**
     * Add alias.
     *
     * @return Person
     */
    public function addAlias(Alias $alias) {
        if ( ! $this->aliases->contains($alias)) {
            $this->aliases[] = $alias;
        }

        return $this;
    }

    /**
     * Remove alias.
     */
    public function removeAlias(Alias $alias) : void {
        $this->aliases->removeElement($alias);
    }

    /**
     * Get aliases.
     *
     * @return Collection
     */
    public function getAliases() {
        return $this->aliases;
    }

    public function getContributions($category = null, $sort = 'year') {
        $data = $this->traitContributions($sort);
        if (null === $category) {
            return $data;
        }

        return array_filter($data, function (Contribution $contribution) use ($category) {
            return $contribution->getPublication()->getCategory() === $category;
        });
    }

    /**
     * Add urlLink.
     *
     * @param string $urlLink
     *
     * @return string
     */
    public function addUrlLink($urlLink) {
        if ( ! in_array($urlLink, $this->urlLinks, true)) {
            $this->urlLinks[] = $urlLink;
        }

        return $this;
    }

    /**
     * Remove urlLink.
     *
     * @param string $urlLink
     */
    public function removeUrlLink($urlLink) {
        $index = array_search($urlLink, $this->urlLinks, true);
        if (false !== $index) {
            unset($this->urlLinks[$index]);
        }

        return $this;
    }

    /**
     * Get urlLinks.
     *
     * @return array
     */
    public function getUrlLinks() {
        return $this->urlLinks;
    }

    /**
     * Set urlLinks.
     *
     * @param string[] $urlLinks
     *
     * @return Person
     */
    public function setUrlLinks(array $urlLinks) {
        $this->urlLinks = $urlLinks;

        return $this;
    }

    /**
     * Set gender.
     *
     * @param string $gender
     *
     * @return Person
     */
    public function setGender($gender) {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender.
     *
     * @return string
     */
    public function getGender() {
        return $this->gender;
    }

    /**
     * Set canadian.
     *
     * @param null|bool $canadian
     *
     * @return Person
     */
    public function setCanadian($canadian = null) {
        $this->canadian = $canadian;

        return $this;
    }

    /**
     * Get canadian.
     *
     * @return null|bool
     */
    public function getCanadian() {
        return $this->canadian;
    }
}
