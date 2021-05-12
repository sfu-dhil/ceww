<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Contribution.
 *
 * @ORM\Table(name="contribution")
 * @ORM\Entity(repositoryClass="App\Repository\ContributionRepository")
 */
class Contribution extends AbstractEntity {
    /**
     * @var Role
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="contributions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $role;

    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="contributions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $person;

    /**
     * @var Publication
     * @ORM\ManyToOne(targetEntity="Publication", inversedBy="contributions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $publication;

    public function __construct() {
        parent::__construct();
    }

    public function __toString() : string {
        return "{$this->role->getName()}:{$this->person->getFullName()}:{$this->publication->getTitle()}";
    }

    /**
     * Set role.
     *
     * @return Contribution
     */
    public function setRole(Role $role) {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role.
     *
     * @return Role
     */
    public function getRole() {
        return $this->role;
    }

    /**
     * Set person.
     *
     * @return Contribution
     */
    public function setPerson(Person $person) {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person.
     *
     * @return Person
     */
    public function getPerson() {
        return $this->person;
    }

    /**
     * Set publication.
     *
     * @return Contribution
     */
    public function setPublication(Publication $publication) {
        $this->publication = $publication;

        return $this;
    }

    /**
     * Get publication.
     *
     * @return Publication
     */
    public function getPublication() {
        return $this->publication;
    }

    /**
     * Get publication id.
     *
     * @return publicaiton
     */
    public function getPublicationId() {
        return $this->publication->getId();
    }
}
