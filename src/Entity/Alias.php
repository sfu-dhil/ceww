<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AliasRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[ORM\Table(name: 'alias')]
#[ORM\Index(columns: ['name'], flags: ['fulltext'])]
#[ORM\Entity(repositoryClass: AliasRepository::class)]
class Alias extends AbstractEntity implements NormalizableInterface {
    #[ORM\Column(type: Types::STRING, length: 100, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: false)]
    private ?string $sortableName = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $maiden = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $married = null;

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
     * @var Collection<Person>
     */
    #[ORM\ManyToMany(targetEntity: Person::class, mappedBy: 'aliases')]
    #[ORM\OrderBy(['sortableName' => 'asc'])]
    private Collection $people;

    public function __construct() {
        parent::__construct();
        $this->people = new ArrayCollection();
        $this->notes = '';
    }

    public function __toString() : string {
        return $this->name;
    }

    public function setName(?string $name) : self {
        $this->name = $name;

        return $this;
    }

    public function getName() : ?string {
        return $this->name;
    }

    public function setMaiden(?bool $maiden) : self {
        $this->maiden = $maiden;

        return $this;
    }

    public function getMaiden(?bool $yesNo = false) : string|bool|null {
        if ($yesNo) {
            if (isset($this->maiden)) {
                return $this->maiden ? 'Yes' : 'No';
            }

            return false;
        }

        return $this->maiden;
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

    public function appendNote(?string $note) : self {
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

    public function addPerson(Person $person) : self {
        $this->people[] = $person;

        return $this;
    }

    public function removePerson(Person $person) : void {
        $this->people->removeElement($person);
    }

    public function getPeople() : Collection {
        return $this->people;
    }

    public function setSortableName(?string $sortableName) : self {
        $this->sortableName = $sortableName;

        return $this;
    }

    public function getSortableName() : ?string {
        return $this->sortableName;
    }

    public function setMarried(?bool $married = null) : self {
        $this->married = $married;

        return $this;
    }

    public function getMarried(?bool $yesNo = false) : bool|string|null {
        if ($yesNo) {
            if (isset($this->married)) {
                return $this->married ? 'Yes' : 'No';
            }

            return null;
        }

        return $this->married;
    }

    public function normalize(NormalizerInterface $serializer, ?string $format = null, array $context = []): array
    {
        return [
            'recordType' => 'Alias',
            'name' => $this->getName(),
            'sortable' => $this->getSortableName(),
            'description' => $this->getDescriptionSanitized(),
            'people' => array_unique(array_map(function ($person) {
                return $person->getFullName();
            }, $this->getPeople()->toArray())),
        ];
    }
}
