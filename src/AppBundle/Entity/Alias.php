<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Alias
 *
 * @ORM\Table(name="alias", indexes={
 *  @ORM\Index(columns="name", flags={"fulltext"})
 * })
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AliasRepository")
 */
class Alias extends AbstractEntity
{
    /**
     * @var string
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private $maiden;
    
    /**
     * public research notes.
     * @var string
     * @ORM\Column(type="text")
     */
    private $description;
    
    /**
     * private research notes.
     * @var string
     * @ORM\Column(type="text")
     */
    private $notes;
    
    /**
     * @var Collection|Person[]
     * @ORM\ManyToMany(targetEntity="Person", mappedBy="aliases")
     */
    private $people;
    
    public function __construct() {
        parent::__construct();
        $this->people = new ArrayCollection();
    }
    
    public function __toString() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Alias
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set maiden
     *
     * @param boolean $maiden
     *
     * @return Alias
     */
    public function setMaiden($maiden)
    {
        $this->maiden = $maiden;

        return $this;
    }

    /**
     * Get maiden
     *
     * @return boolean
     */
    public function getMaiden()
    {
        return $this->maiden;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Alias
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return Alias
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Add person
     *
     * @param Person $person
     *
     * @return Alias
     */
    public function addPerson(Person $person)
    {
        $this->people[] = $person;

        return $this;
    }

    /**
     * Remove person
     *
     * @param Person $person
     */
    public function removePerson(Person $person)
    {
        $this->people->removeElement($person);
    }

    /**
     * Get people
     *
     * @return Collection
     */
    public function getPeople()
    {
        return $this->people;
    }
}
