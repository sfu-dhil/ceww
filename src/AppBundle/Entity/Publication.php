<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Publication
 *
 * @ORM\Table(name="publication")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PublicationRepository")
 */
class Publication extends AbstractEntity {
    
    /**
     * @ORM\Column(type="string", length=250, nullable=false)
     * @Groups({"public", "private"})
     */
    private $title;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"public", "private"})
     */
    private $year;
    
    /**
     * @ORM\ManyToOne(targetEntity="PublicationType", inversedBy="publications")
     * @ORM\JoinColumn(name="publication_type_id")
     * @Groups({"recursive"})
     * @var PublicationType
     */
    private $publicationType;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"private"})
     */
    private $notes;
    
    /**
     * @ORM\ManyToMany(targetEntity="Author", mappedBy="publications")
     * @Groups({"recursive"})
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
     * @param PublicationType $publicationType
     *
     * @return Publication
     */
    public function setPublicationType(PublicationType $publicationType = null)
    {
        $this->publicationType = $publicationType;

        return $this;
    }

    /**
     * Get publicationType
     *
     * @return PublicationType
     */
    public function getPublicationType()
    {
        return $this->publicationType;
    }

    /**
     * Add author
     *
     * @param Author $author
     *
     * @return Publication
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
