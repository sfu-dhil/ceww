<?php

namespace AppBundle\Entity;

use AppBundle\Entity\AbstractEntity;
use AppBundle\Entity\Genre;
use AppBundle\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Genre
 *
 * @ORM\Table(name="genre")
 * @ORM\Entity(repositoryClass="GenreRepository")
 */
class Genre extends AbstractEntity
{

    /**
     * @ORM\Column(type="string", length=120, nullable=false)
     * @Groups({"public", "private"})
     */
    private $name;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"private"})
     */
    private $description;
    
    /**
     * @ORM\ManyToMany(targetEntity="Publication", mappedBy="genres")
     * @Groups({"public", "private"})
     * @var Collection|Publication[]
     */
    private $publications;

    public function __toString() {
        return $this->name;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->publications = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Genre
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
     * Set description
     *
     * @param string $description
     *
     * @return Genre
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
     * Add publication
     *
     * @param Publication $publication
     *
     * @return Genre
     */
    public function addPublication(Publication $publication)
    {
        $this->publications[] = $publication;

        return $this;
    }

    /**
     * Remove publication
     *
     * @param Publication $publication
     */
    public function removePublication(Publication $publication)
    {
        $this->publications->removeElement($publication);
    }

    /**
     * Get publications
     *
     * @return Collection|Publication[]
     */
    public function getPublications()
    {
        return $this->publications;
    }
}
