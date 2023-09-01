<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use FOS\ElasticaBundle\Transformer\HighlightableModelInterface;
use Nines\MediaBundle\Entity\LinkableInterface;
use Nines\MediaBundle\Entity\LinkableTrait;
use Nines\UtilBundle\Entity\AbstractEntity;

#[ORM\Table(name: 'person')]
#[ORM\Index(columns: ['full_name'], flags: ['fulltext'])]
#[ORM\Index(columns: ['sortable_name'])]
#[ORM\Entity(repositoryClass: PersonRepository::class)]
class Person extends AbstractEntity implements LinkableInterface, HighlightableModelInterface {
    use HasContributions {
        HasContributions::__construct as private trait_constructor;
        getContributions as private traitContributions;
    }
    use LinkableTrait {
        LinkableTrait::__construct as private link_constructor;
    }
    use HasHighlights;

    public const MALE = 'm';

    public const FEMALE = 'f';

    #[ORM\Column(type: Types::STRING, length: 200, nullable: false)]
    private ?string $fullName = null;

    #[ORM\Column(type: Types::STRING, length: 191, nullable: false)]
    private ?string $sortableName = null;

    #[ORM\Column(type: Types::STRING, length: 1, nullable: true)]
    private ?string $gender = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['default' => true])]
    private ?bool $canadian = null;

    /**
     * public research notes.
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * private research notes.
     */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    /**
     * @var string[]
     */
    #[ORM\Column(type: Types::ARRAY)]
    private array $urlLinks;

    #[ORM\OneToOne(targetEntity: DateYear::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?DateYear $birthDate = null;

    #[ORM\ManyToOne(targetEntity: Place::class, inversedBy: 'peopleBorn')]
    private ?Place $birthPlace = null;

    #[ORM\OneToOne(targetEntity: DateYear::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?DateYear $deathDate = null;

    #[ORM\ManyToOne(targetEntity: Place::class, inversedBy: 'peopleDied')]
    private ?Place $deathPlace = null;

    /**
     * @var Collection<Place>
     */
    #[ORM\ManyToMany(targetEntity: Place::class, inversedBy: 'residents')]
    #[ORM\OrderBy(['sortableName' => 'asc'])]
    private Collection $residences;

    /**
     * @var Collection<Alias>
     */
    #[ORM\ManyToMany(targetEntity: Alias::class, inversedBy: 'people')]
    #[ORM\OrderBy(['sortableName' => 'asc'])]
    private Collection $aliases;

    /**
     * @var Collection<Contribution>
     */
    #[ORM\OneToMany(targetEntity: Contribution::class, mappedBy: 'person', orphanRemoval: true)]
    private Collection $contributions;

    public function __construct() {
        $this->contributions = new ArrayCollection();
        parent::__construct();
        $this->trait_constructor();
        $this->link_constructor();
        $this->canadian = true;
        $this->residences = new ArrayCollection();
        $this->aliases = new ArrayCollection();
        $this->urlLinks = [];
    }

    public function __toString() : string {
        return $this->getFullName();
    }

    public function setFullName(?string $fullName) : self {
        $this->fullName = $fullName;

        return $this;
    }

    public function getFullName() : string {
        if ($this->fullName) {
            return $this->fullName;
        }

        return '(unknown)';
    }

    public function setSortableName(?string $sortableName) : self {
        $this->sortableName = $sortableName;

        return $this;
    }

    public function getSortableName() : ?string {
        return $this->sortableName;
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

    public function getNotes() : ?string {
        return $this->notes;
    }

    public function setBirthDate(DateYear|string|null $birthDate = null) : self {
        if (is_string($birthDate) || is_numeric($birthDate)) {
            $dateYear = new DateYear();
            $dateYear->setValue($birthDate);
            $this->birthDate = $dateYear;
        } else {
            $this->birthDate = $birthDate;
        }

        return $this;
    }

    public function getBirthDate() : ?DateYear {
        return $this->birthDate;
    }

    public function setBirthPlace(?Place $birthPlace = null) : self {
        $this->birthPlace = $birthPlace;

        return $this;
    }

    public function getBirthPlace() : ?Place {
        return $this->birthPlace;
    }

    public function setDeathDate(DateYear|string|null $deathDate = null) : self {
        if (is_string($deathDate) || is_numeric($deathDate)) {
            $dateYear = new DateYear();
            $dateYear->setValue($deathDate);
            $this->deathDate = $dateYear;
        } else {
            $this->deathDate = $deathDate;
        }

        return $this;
    }

    public function getDeathDate() : ?DateYear {
        return $this->deathDate;
    }

    public function getDeathYear() : mixed {
        if ($this->deathDate) {
            return $this->deathDate->getStart(false);
        }

        return null;
    }

    public function setDeathPlace(?Place $deathPlace = null) : self {
        $this->deathPlace = $deathPlace;

        return $this;
    }

    public function getDeathPlace() : ?Place {
        return $this->deathPlace;
    }

    public function addResidence(Place $residence) : self {
        if ( ! $this->residences->contains($residence)) {
            $this->residences[] = $residence;
        }

        return $this;
    }

    public function removeResidence(Place $residence) : void {
        $this->residences->removeElement($residence);
    }

    public function getResidences() : Collection {
        return $this->residences;
    }

    public function addAlias(Alias $alias) : self {
        if ( ! $this->aliases->contains($alias)) {
            $this->aliases[] = $alias;
        }

        return $this;
    }

    public function removeAlias(Alias $alias) : void {
        $this->aliases->removeElement($alias);
    }

    public function getAliases() : Collection {
        return $this->aliases;
    }

    /**
     * @return Contribution[]
     */
    public function getContributions(mixed $category = null, string $sort = 'year') : array {
        $data = $this->traitContributions($sort);
        if (null === $category) {
            return $data;
        }

        return array_filter($data, fn (Contribution $contribution) => $contribution->getPublication()->getCategory() === $category);
    }

    public function addUrlLink(?string $urlLink) : self {
        if ( ! in_array($urlLink, $this->urlLinks, true)) {
            $this->urlLinks[] = $urlLink;
        }

        return $this;
    }

    public function removeUrlLink(?string $urlLink) : self {
        $index = array_search($urlLink, $this->urlLinks, true);
        if (false !== $index) {
            unset($this->urlLinks[$index]);
        }

        return $this;
    }

    public function getUrlLinks() : array {
        return $this->urlLinks;
    }

    public function setUrlLinks(array $urlLinks) : self {
        $this->urlLinks = $urlLinks;

        return $this;
    }

    public function setGender(?string $gender) : self {
        $this->gender = $gender;

        return $this;
    }

    public function getGender() : ?string {
        return $this->gender;
    }

    public function setCanadian(?bool $canadian = null) : self {
        $this->canadian = $canadian;

        return $this;
    }

    public function getCanadian() : ?bool {
        return $this->canadian;
    }
}
