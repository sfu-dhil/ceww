<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category extends AbstractEntity
{

    /**
     * @ORM\Column(type="string", length=24, nullable=false)
     * @Groups({"public", "private"})
     */
    private $label;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"private"})
     */
    private $description;
    
    /**
     * @ORM\OneToMany(targetEntity="Publication", mappedBy="category")
     * @var Collection|Publication[]
     * @Groups({"recursive"})
     */
    private $publications;
    
    public function __toString() {
        return $this->label;
    }


    /**
     * Set label
     *
     * @param string $label
     *
     * @return Category
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Category
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
     * Constructor
     */
    public function __construct()
    {
        $this->publications = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add publication
     *
     * @param \AppBundle\Entity\Publication $publication
     *
     * @return Category
     */
    public function addPublication(\AppBundle\Entity\Publication $publication)
    {
        $this->publications[] = $publication;

        return $this;
    }

    /**
     * Remove publication
     *
     * @param \AppBundle\Entity\Publication $publication
     */
    public function removePublication(\AppBundle\Entity\Publication $publication)
    {
        $this->publications->removeElement($publication);
    }

    /**
     * Get publications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPublications()
    {
        return $this->publications;
    }
}
