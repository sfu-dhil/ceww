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
use Nines\UtilBundle\Entity\AbstractEntity;
use Nines\SolrBundle\Annotation as Solr;

/**
 * Alias.
 *
 * @ORM\Table(name="alias", indexes={
 *     @ORM\Index(columns={"name"}, flags={"fulltext"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\AliasRepository")
 * @Solr\Document(
 *      @Solr\CopyField(from={"name","description","people"}, to="content", type="texts")
 * )
 */
class Alias extends AbstractEntity {
    /**
     * Name of the alias.
     *
     * @var string
     * @ORM\Column(type="string", length=100, nullable=false)
     * @Solr\Field(type="text", boost=2.5)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=100, nullable=false)
     * @Solr\Field(name="sortable", type="string", boost=0.2)
     */
    private $sortableName;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     * @Solr\Field(type="string", getter="getMaiden(true)")
     */
    private $maiden;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     * @Solr\Field(type="string", getter="getMarried(true)")
     */
    private $married;

    /**
     * public research notes.
     *
     * @var string
     * @ORM\Column(type="text", nullable=true)
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
     * @var Collection|Person[]
     * @ORM\ManyToMany(targetEntity="Person", mappedBy="aliases")
     * @ORM\OrderBy({"sortableName": "ASC"})
     * @Solr\Field(type="texts", boost=1.3, getter="getPeople(true)")
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
    public function getMaiden(?bool $yesNo = false) {
        if($yesNo) {
            if(isset($this->maiden)) {
                return $this->maiden ? 'Yes' : 'No';
            }
            return false;
        }
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
     * @return Collection|array<string>
     */
    public function getPeople(?bool $flatten = false) {
        if($flatten) {
            return array_map(fn (Person $p) => $p->getFullName(), $this->people->toArray());
        }
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
    public function getMarried(?bool $yesNo = false) {
        if($yesNo) {
            if(isset($this->married)) {
                return $this->married ? 'Yes' : 'No';
            }
            return null;
        }
        return $this->married;
    }
}
