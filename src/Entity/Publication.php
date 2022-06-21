<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
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
 * Publication.
 *
 * @ORM\Entity(repositoryClass="App\Repository\PublicationRepository")
 * @ORM\Table(name="publication", indexes={
 *     @ORM\Index(columns={"title"}, flags={"fulltext"}),
 *     @ORM\Index(columns={"sortable_title"}, flags={"fulltext"}),
 *     @ORM\Index(columns={"category"}),
 * })
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="category", type="string", length=64)
 * @ORM\DiscriminatorMap({
 *     "book": "Book",
 *     "compilation": "Compilation",
 *     "periodical": "Periodical"
 * })
 */
abstract class Publication extends AbstractEntity implements LinkableInterface {
    use HasContributions {
        HasContributions::__construct as private trait_constructor;

    }
    use LinkableTrait {
        LinkableTrait::__construct as private link_constructor;

    }

    public const BOOK = 'book';

    public const COMPILATION = 'compilation';

    public const PERIODICAL = 'periodical';

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     *
     * @Solr\Field(type="text", boost=2.0)
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     *
     * @Solr\Field(name="sortable", type="string", boost=0.2)
     */
    private $sortableTitle;

    /**
     * @var string[]
     * @ORM\Column(type="array", name="links")
     */
    private $oldLinks;

    /**
     * public research notes.
     *
     * @var string
     * @ORM\Column(type="text", nullable=true)
     *
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
     * @var DateYear
     * @ORM\OneToOne(targetEntity="DateYear", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @Solr\Field(type="integer", mutator="getYear")
     */
    private $dateYear;

    /**
     * @var Place
     * @ORM\ManyToOne(targetEntity="Place", inversedBy="publications")
     *
     * @Solr\Field(type="text", boost=0.5, mutator="getName")
     */
    private $location;

    /**
     * @var Collection|Genre[]
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="publications")
     * @ORM\JoinTable(name="publications_genres")
     * @ORM\OrderBy({"label": "ASC"})
     *
     * @Solr\Field(type="texts", boost=0.5, getter="getGenres(true)")
     */
    private $genres;

    /**
     * @var Collection|Contribution[]
     * @ORM\OneToMany(targetEntity="Contribution", mappedBy="publication", cascade={"persist"}, orphanRemoval=true)
     *
     * @Solr\Field(type="texts", boost=0.5, getter="getContributors()")
     */
    private $contributions;

    /**
     * @var Collection|Publisher
     * @ORM\ManyToMany(targetEntity="Publisher", inversedBy="publications")
     * @ORM\OrderBy({"name": "ASC"})
     *
     * @Solr\Field(type="texts", boost=0.5, getter="getPublishers(true)")
     */
    private $publishers;

    public function __construct() {
        parent::__construct();
        $this->trait_constructor();
        $this->link_constructor();
        $this->oldLinks = new ArrayCollection();
        $this->genres = new ArrayCollection();
        $this->publishers = new ArrayCollection();
    }

    public function __toString() : string {
        return $this->title;
    }

    abstract public function getCategory();

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Publication
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set sortableTitle.
     *
     * @param string $sortableTitle
     *
     * @return Publication
     */
    public function setSortableTitle($sortableTitle) {
        $this->sortableTitle = $sortableTitle;

        return $this;
    }

    /**
     * Get sortableTitle.
     *
     * @return string
     */
    public function getSortableTitle() {
        if ($this->sortableTitle) {
            return $this->sortableTitle;
        }

        return $this->title;
    }

    /**
     * Set links.
     *
     * @param array $links
     *
     * @return Publication
     */
    public function setOldLinks($links) {
        if ( ! $links instanceof ArrayCollection) {
            $this->oldLinks = new ArrayCollection($links);
        } else {
            $this->oldLinks = $links;
        }

        return $this;
    }

    public function addOldLink($link) {
        if ( ! $this->oldLinks instanceof ArrayCollection) {
            $this->oldLinks = new ArrayCollection($this->oldLinks);
        }
        if ( ! $this->oldLinks->contains($link)) {
            $this->oldLinks->add($link);
        }

        return $this;
    }

    /**
     * Get links.
     *
     * @return array
     */
    public function getOldLinks() {
        $data = $this->oldLinks;
        if ($this->oldLinks instanceof ArrayCollection) {
            $data = $this->oldLinks->toArray();
        }
        usort($data, fn ($a, $b) => mb_substr($a, mb_strpos($a, '//') + 1) <=> mb_substr($b, mb_strpos($b, '//') + 1));

        return $data;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Publication
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
     * @return Publication
     */
    public function setNotes($notes) {
        $this->notes = $notes;

        return $this;
    }

    public function appendNote($note) {
        if ( ! $this->notes) {
            $this->notes = $note;
        } else {
            $this->notes .= "\n\n" . $note;
        }

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
     * Set dateYear.
     *
     * @param DateYear|string $dateYear
     *
     * @return Publication
     */
    public function setDateYear($dateYear = null) {
        if (is_string($dateYear) || is_numeric($dateYear)) {
            $obj = new DateYear();
            $obj->setValue($dateYear);
            $this->dateYear = $obj;
        } else {
            $this->dateYear = $dateYear;
        }

        return $this;
    }

    /**
     * Get dateYear.
     *
     * @return DateYear
     */
    public function getDateYear() {
        return $this->dateYear;
    }

    /**
     * Set location.
     *
     * @param Place $location
     *
     * @return Publication
     */
    public function setLocation(?Place $location = null) {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location.
     *
     * @return Place
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * Set genres.
     *
     * @param Collection|Genre[] $genres
     */
//    public function setGenres(Collection $genres) {
    public function setGenres($genres) : void {
        if (is_array($genres)) {
            $this->genres = new ArrayCollection($genres);
        } else {
            $this->genres = $genres;
        }
    }

    /**
     * Add genre.
     *
     * @return Publication
     */
    public function addGenre(Genre $genre) {
        if ( ! $this->genres->contains($genre)) {
            $this->genres[] = $genre;
        }

        return $this;
    }

    /**
     * Remove genre.
     */
    public function removeGenre(Genre $genre) : void {
        $this->genres->removeElement($genre);
    }

    /**
     * Get genres.
     *
     * @param ?bool $flat
     *
     * @return Collection
     */
    public function getGenres(?bool $flat = false) {
        if ($flat) {
            return array_map(fn (Genre $p) => $p->getLabel(), $this->genres->toArray());
        }

        return $this->genres;
    }

    /**
     * Get the first author contributor for a publication or null if there
     * are no author contributors.
     *
     * @return null|Person
     */
    public function getFirstAuthor() {
        foreach ($this->contributions as $contribution) {
            if ('author' === $contribution->getRole()->getName()) {
                return $contribution->getPerson();
            }
        }
    }

    /**
     * Get the first contribution for a publication.
     *
     * @return Contribution
     */
    public function getFirstContribution() {
        return $this->contributions->first();
    }

    public function getContributors() {
        return array_map(
            fn (Contribution $c) => $c->getPerson()->getFullName(),
            $this->contributions->toArray()
        );
    }

    /**
     * Add publisher.
     *
     * @return Publication
     */
    public function addPublisher(Publisher $publisher) {
        $this->publishers[] = $publisher;

        return $this;
    }

    /**
     * Remove publisher.
     */
    public function removePublisher(Publisher $publisher) : void {
        $this->publishers->removeElement($publisher);
    }

    /**
     * Get publishers.
     *
     * @param mixed $flatten
     *
     * @return array|Collection
     */
    public function getPublishers($flatten = false) {
        if ($flatten) {
            return array_map(
                fn (Publisher $p) => $p->getName(),
                $this->publishers->toArray()
            );
        }

        return $this->publishers;
    }

    /**
     * @param array|Collection $publishers
     */
    public function setPublishers($publishers) : void {
        if (is_array($publishers)) {
            $this->publishers = new ArrayCollection($publishers);
        } else {
            $this->publishers = $publishers;
        }
    }
}
