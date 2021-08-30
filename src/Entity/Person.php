<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\MediaBundle\Entity\LinkableInterface;
use Nines\MediaBundle\Entity\LinkableTrait;
use Nines\SolrBundle\Annotation as Solr;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Person.
 *
 * @ORM\Table(name="person", indexes={
 *     @ORM\Index(columns={"full_name"}, flags={"fulltext"}),
 *     @ORM\Index(columns={"sortable_name"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\PersonRepository")
 *
 * @Solr\Document(
 *     @Solr\CopyField(from={"fullName", "description", "birthPlace", "residences", "aliases", "deathPlace"}, to="content", type="texts")
 * )
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
     * @Solr\Field(type="text", boost=2.5)
     */
    private $fullName;

    /**
     * @var string
     * @ORM\Column(type="string", length=200, nullable=false)
     * @Solr\Field(name="sortable", type="string", boost=0.2)
     */
    private $sortableName;

    /**
     * @var string
     * @ORM\Column(type="string", length=1, nullable=true)
     * @Solr\Field(type="string")
     */
    private $gender;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true, options={"default": true})
     * @Solr\Field(type="boolean")
     */
    private $canadian;

    /**
     * public research notes.
     *
     * @var string
     * @ORM\Column(type="text", nullable=true)
     *
     * Note that ENT_QUOTES | ENT_HTML5 === 51
     * @Solr\Field(type="text", boost=0.5, filters={"strip_tags", "html_entity_decode(51, 'UTF-8')"})
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
     * @Solr\Field(type="integer", getter="getBirthYear")
     */
    private $birthDate;

    /**
     * @var Place
     * @ORM\ManyToOne(targetEntity="Place", inversedBy="peopleBorn")
     * @Solr\Field(type="string", boost=0.4, mutator="getName")
     */
    private $birthPlace;

    /**
     * @var DateYear
     * @ORM\OneToOne(targetEntity="DateYear", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Solr\Field(type="integer", getter="getDeathYear")
     */
    private $deathDate;

    /**
     * @var Place;
     * @ORM\ManyToOne(targetEntity="Place", inversedBy="peopleDied")
     * @Solr\Field(type="string", boost=0.4, mutator="getName")
     */
    private $deathPlace;

    /**
     * @var Collection|Place[]
     *
     * @ORM\ManyToMany(targetEntity="Place", inversedBy="residents")
     * @ORM\OrderBy({"sortableName": "ASC"})
     *
     * @Solr\Field(type="texts", boost=0.3, getter="getResidences(true)")
     */
    private $residences;

    /**
     * @var Collection|Place[]
     * @ORM\ManyToMany(targetEntity="Alias", inversedBy="people")
     * @ORM\OrderBy({"sortableName": "ASC"})
     *
     * @Solr\Field(type="texts", boost=1.3, getter="getAliases(true)")
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

    public function getBirthYear() {
        if ($this->birthDate) {
            return $this->birthDate->getStart(false);
        }
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

    public function getDeathYear() {
        if ($this->deathDate) {
            return $this->deathDate->getStart(false);
        }
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
     * @param ?bool $flat
     *
     * @return Collection
     */
    public function getResidences(?bool $flat = false) {
        if ($flat) {
            return array_map(fn (Place $p) => $p->getName(), $this->residences->toArray());
        }

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
     * @param ?bool $flat
     *
     * @return Collection
     */
    public function getAliases(?bool $flat = false) {
        if ($flat) {
            return array_map(fn (Alias $a) => $a->getName(), $this->aliases->toArray());
        }

        return $this->aliases;
    }

    public function getContributions($category = null, $sort = 'year') {
        $data = $this->traitContributions($sort);
        if (null === $category) {
            return $data;
        }

        return array_filter($data, fn (Contribution $contribution) => $contribution->getPublication()->getCategory() === $category);
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
