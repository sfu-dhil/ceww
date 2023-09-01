<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ContributionRepository;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

#[ORM\Table(name: 'contribution')]
#[ORM\Entity(repositoryClass: ContributionRepository::class)]
class Contribution extends AbstractEntity {
    #[ORM\ManyToOne(targetEntity: Role::class, inversedBy: 'contributions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Role $role = null;

    #[ORM\ManyToOne(targetEntity: Person::class, inversedBy: 'contributions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Person $person = null;

    #[ORM\ManyToOne(targetEntity: Publication::class, inversedBy: 'contributions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Publication $publication = null;

    public function __construct() {
        parent::__construct();
    }

    public function __toString() : string {
        return "{$this->role->getName()}:{$this->person->getFullName()}:{$this->publication->getTitle()}";
    }

    public function setRole(Role $role) : self {
        $this->role = $role;

        return $this;
    }

    public function getRole() : ?Role {
        return $this->role;
    }

    public function setPerson(Person $person) : self {
        $this->person = $person;

        return $this;
    }

    public function getPerson() : ?Person {
        return $this->person;
    }

    public function setPublication(Publication $publication) : self {
        $this->publication = $publication;

        return $this;
    }

    public function getPublication() : ?Publication {
        return $this->publication;
    }

    public function getPublicationId() : ?int {
        return $this->publication->getId();
    }
}
