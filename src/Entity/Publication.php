<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PublicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use FOS\ElasticaBundle\Transformer\HighlightableModelInterface;
use Nines\MediaBundle\Entity\LinkableInterface;
use Nines\MediaBundle\Entity\LinkableTrait;
use Nines\UtilBundle\Entity\AbstractEntity;

#[ORM\Table(name: 'publication')]
#[ORM\Index(columns: ['title'], flags: ['fulltext'])]
#[ORM\Index(columns: ['sortable_title'], flags: ['fulltext'])]
#[ORM\Index(columns: ['category'])]
#[ORM\Entity(repositoryClass: PublicationRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'category', type: 'string', length: 64)]
#[ORM\DiscriminatorMap(['book' => 'Book', 'compilation' => 'Compilation', 'periodical' => 'Periodical'])]
abstract class Publication extends AbstractEntity implements LinkableInterface, HighlightableModelInterface {
    use HasContributions {
        HasContributions::__construct as private trait_constructor;
    }
    use LinkableTrait {
        LinkableTrait::__construct as private link_constructor;
    }
    use HasHighlights;

    public const BOOK = 'book';

    public const COMPILATION = 'compilation';

    public const PERIODICAL = 'periodical';

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private ?string $sortableTitle = null;

    #[ORM\Column(type: Types::ARRAY, name: 'links')]
    private Collection|array $oldLinks;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\OneToOne(targetEntity: DateYear::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?DateYear $dateYear = null;

    #[ORM\ManyToOne(targetEntity: Place::class, inversedBy: 'publications')]
    private ?Place $location = null;

    /**
     * @var Collection<Genre>
     */
    #[ORM\JoinTable(name: 'publications_genres')]
    #[ORM\ManyToMany(targetEntity: Genre::class, inversedBy: 'publications')]
    #[ORM\OrderBy(['label' => 'asc'])]
    private Collection $genres;

    /**
     * @var Collection<Contribution>
     */
    #[ORM\OneToMany(targetEntity: Contribution::class, mappedBy: 'publication', cascade: ['persist'], orphanRemoval: true)]
    private Collection $contributions;

    /**
     * @var Collection<Publisher>
     */
    #[ORM\ManyToMany(targetEntity: Publisher::class, inversedBy: 'publications')]
    #[ORM\OrderBy(['name' => 'asc'])]
    private Collection $publishers;

    public function __construct() {
        $this->contributions = new ArrayCollection();
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

    public function setTitle(?string $title) : self {
        $this->title = $title;

        return $this;
    }

    public function getTitle() : ?string {
        return $this->title;
    }

    public function setSortableTitle(?string $sortableTitle) : self {
        $this->sortableTitle = $sortableTitle;

        return $this;
    }

    public function getSortableTitle() : ?string {
        if ($this->sortableTitle) {
            return $this->sortableTitle;
        }

        return $this->title;
    }

    public function setOldLinks(array|ArrayCollection $links) : self {
        if ( ! $links instanceof ArrayCollection) {
            $this->oldLinks = new ArrayCollection($links);
        } else {
            $this->oldLinks = $links;
        }

        return $this;
    }

    public function addOldLink(string $link) : self {
        if ( ! $this->oldLinks instanceof ArrayCollection) {
            $this->oldLinks = new ArrayCollection($this->oldLinks);
        }
        if ( ! $this->oldLinks->contains($link)) {
            $this->oldLinks->add($link);
        }

        return $this;
    }

    public function getOldLinks() : array {
        $data = $this->oldLinks;
        if ($this->oldLinks instanceof ArrayCollection) {
            $data = $this->oldLinks->toArray();
        }
        usort($data, fn ($a, $b) => mb_substr((string) $a, mb_strpos((string) $a, '//') + 1) <=> mb_substr((string) $b, mb_strpos((string) $b, '//') + 1));

        return $data;
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

    public function appendNote(string $note) : self {
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

    public function setDateYear(DateYear|string|null $dateYear = null) : self {
        if (is_string($dateYear) || is_numeric($dateYear)) {
            $obj = new DateYear();
            $obj->setValue($dateYear);
            $this->dateYear = $obj;
        } else {
            $this->dateYear = $dateYear;
        }

        return $this;
    }

    public function getDateYear() : ?DateYear {
        return $this->dateYear;
    }

    public function setLocation(?Place $location = null) : self {
        $this->location = $location;

        return $this;
    }

    public function getLocation() : ?Place {
        return $this->location;
    }

    /**
     * @param Collection<Genre>|Genre[] $genres
     */
    public function setGenres(Collection|array $genres) : self {
        if (is_array($genres)) {
            $this->genres = new ArrayCollection($genres);
        } else {
            $this->genres = $genres;
        }

        return $this;
    }

    public function addGenre(Genre $genre) : self {
        if ( ! $this->genres->contains($genre)) {
            $this->genres[] = $genre;
        }

        return $this;
    }

    public function removeGenre(Genre $genre) : void {
        $this->genres->removeElement($genre);
    }

    public function getGenres() : Collection {
        return $this->genres;
    }

    public function getFirstAuthor() : ?Person {
        foreach ($this->contributions as $contribution) {
            if ('author' === $contribution->getRole()->getName()) {
                return $contribution->getPerson();
            }
        }

        return null;
    }

    public function getFirstContribution() : ?Contribution {
        return $this->contributions->first();
    }

    public function addPublisher(Publisher $publisher) : self {
        $this->publishers[] = $publisher;

        return $this;
    }

    public function removePublisher(Publisher $publisher) : void {
        $this->publishers->removeElement($publisher);
    }

    /**
     * @return Collection<Publisher>
     */
    public function getPublishers() : Collection {
        return $this->publishers;
    }

    /**
     * @param Collection<Publisher>|Publisher[] $publishers
     */
    public function setPublishers(Collection|array $publishers) : void {
        if (is_array($publishers)) {
            $this->publishers = new ArrayCollection($publishers);
        } else {
            $this->publishers = $publishers;
        }
    }
}
