<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Status
 *
 * @ORM\Table(name="status")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StatusRepository")
 * @UniqueEntity("label")
 */
class Status extends AbstractEntity
{

    /**
     * @ORM\Column(type="string", length=24, nullable=false)
     */
    private $label;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $public;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="Author", mappedBy="status")
     * @var Collection|Author[]
     */
    private $publishedAuthors;

    /**
     * Constructor
     */
    public function __construct() {
        $this->public = false;
        $this->publishedAuthors = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString() {
        return $this->label;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return Status
     */
    public function setLabel($label) {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Status
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Add publishedAuthor
     *
     * @param \AppBundle\Entity\Author $publishedAuthor
     *
     * @return Status
     */
    public function addPublishedAuthor(\AppBundle\Entity\Author $publishedAuthor) {
        $this->publishedAuthors[] = $publishedAuthor;

        return $this;
    }

    /**
     * Remove publishedAuthor
     *
     * @param \AppBundle\Entity\Author $publishedAuthor
     */
    public function removePublishedAuthor(\AppBundle\Entity\Author $publishedAuthor) {
        $this->publishedAuthors->removeElement($publishedAuthor);
    }

    /**
     * Get publishedAuthors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPublishedAuthors() {
        return $this->publishedAuthors;
    }

    /**
     * Set public
     *
     * @param boolean $public
     *
     * @return Status
     */
    public function setPublic($public) {
        $this->public = $public;

        return $this;
    }

    /**
     * Get public
     *
     * @return boolean
     */
    public function getPublic() {
        return $this->public;
    }

}
