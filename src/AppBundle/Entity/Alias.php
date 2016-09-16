<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Alias
 *
 * @ORM\Table(name="alias")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AliasRepository")
 */
class Alias extends AbstractEntity
{

    /**
     * @ORM\Column(type="string", length=120, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $maiden;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="Author", mappedBy="aliases")
     * @var Collection|Author[]
     */
    private $authors;

    public function __construct() {
        $this->maiden = false;
        $this->authors = new ArrayCollection();
    }
    
    public function __toString() {
        return $this->name;
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
     * Add author
     *
     * @param Author $author
     *
     * @return Alias
     */
    public function addAuthor(Author $author)
    {
        $this->authors[] = $author;

        return $this;
    }

    /**
     * Remove author
     *
     * @param Author $author
     */
    public function removeAuthor(Author $author)
    {
        $this->authors->removeElement($author);
    }

    /**
     * Get authors
     *
     * @return Collection
     */
    public function getAuthors()
    {
        return $this->authors;
    }
}
