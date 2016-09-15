<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Publication
 *
 * @ORM\Table(name="publication")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PublicationRepository")
 */
class Publication extends AbstractEntity {
    
    /**
     * @ORM\Column(type="string", length=120, nullable=false)
     */
    private $title;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $year;
    
    /**
     * @ORM\ManyToOne(targetEntity="PublicationType", inversedBy="publications")
     * @ORM\JoinColumn(name="publication_type_id")
     * @var PublicationType
     */
    private $publicationType;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;
    
    /**
     * @ORM\ManyToMany(targetEntity="Author", mappedBy="publications")
     * @var Collection|Author[]
     */
    private $authors;

    public function __construct() {
        $this->authors = new ArrayCollection();
    }
    
    public function __toString() {
        return $this->title;        
    }


    /**
     * Set title
     *
     * @param string $title
     *
     * @return Publication
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set year
     *
     * @param integer $year
     *
     * @return Publication
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return Publication
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
     * Set publicationType
     *
     * @param \AppBundle\Entity\PublicationType $publicationType
     *
     * @return Publication
     */
    public function setPublicationType(\AppBundle\Entity\PublicationType $publicationType = null)
    {
        $this->publicationType = $publicationType;

        return $this;
    }

    /**
     * Get publicationType
     *
     * @return \AppBundle\Entity\PublicationType
     */
    public function getPublicationType()
    {
        return $this->publicationType;
    }

    /**
     * Add author
     *
     * @param \AppBundle\Entity\Author $author
     *
     * @return Publication
     */
    public function addAuthor(\AppBundle\Entity\Author $author)
    {
        $this->authors[] = $author;

        return $this;
    }

    /**
     * Remove author
     *
     * @param \AppBundle\Entity\Author $author
     */
    public function removeAuthor(\AppBundle\Entity\Author $author)
    {
        $this->authors->removeElement($author);
    }

    /**
     * Get authors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuthors()
    {
        return $this->authors;
    }
}
