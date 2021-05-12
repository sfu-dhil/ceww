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
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Alias.
 *
 * @ORM\Table(name="alias", indexes={
 *     @ORM\Index(columns="name", flags={"fulltext"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\AliasRepository")
 */
class Alias extends AbstractEntity {
    /**
     * Name of the alias.
     *
     * @var string
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $sortableName;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $maiden;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $married;

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
     * @var Collection|Person[]
     * @ORM\ManyToMany(targetEntity="Person", mappedBy="aliases")
     * @ORM\OrderBy({"sortableName": "ASC"})
     */
    private $people;

    public function __construct() {
        parent::__construct();
        $this->people = new ArrayCollection();
        $this->notes = '';
    }

    public function __toString() : string {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Alias
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set maiden.
     *
     * @param bool $maiden
     *
     * @return Alias
     */
    public function setMaiden($maiden) {
        $this->maiden = $maiden;

        return $this;
    }

    /**
     * Get maiden.
     *
     * @return bool
     */
    public function getMaiden() {
        return $this->maiden;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Alias
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
     * @return Alias
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
     * Add person.
     *
     * @return Alias
     */
    public function addPerson(Person $person) {
        $this->people[] = $person;

        return $this;
    }

    /**
     * Remove person.
     */
    public function removePerson(Person $person) : void {
        $this->people->removeElement($person);
    }

    /**
     * Get people.
     *
     * @return Collection
     */
    public function getPeople() {
        return $this->people;
    }

    /**
     * Set sortableName.
     *
     * @param string $sortableName
     *
     * @return Alias
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
     * Set married.
     *
     * @param null|bool $married
     *
     * @return Alias
     */
    public function setMarried($married = null) {
        $this->married = $married;

        return $this;
    }

    /**
     * Get married.
     *
     * @return null|bool
     */
    public function getMarried() {
        return $this->married;
    }
}
