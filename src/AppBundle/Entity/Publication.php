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
 * @ORM\HasLifecycleCallbacks
 */
class Publication extends AbstractEntity
{
    
    /**
     * @ORM\Column(type="string", length=250, nullable=false)
     * @Groups({"public", "private"})
     */
    private $title;
    
    /**
     * @ORM\Column(type="string", length=250, nullable=false)
     * @Groups({"private"})
     */
    private $sortableTitle;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"public", "private"})
     */
    private $year;
    
    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="publications")
     * @ORM\JoinColumn(name="category_id")
     * @Groups({"recursive"})
     * @var Category
     */
    private $category;
    
    /**
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="publications")
     * @ORM\JoinTable(name="publication_genres")
     * @Groups({"recursive"})
     * @var Collection|Genre[]
     */
    private $genres;
    
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

    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->genres = new ArrayCollection();
    }
    
    public function __toString()
    {
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
     * Set category
     *
     * @param Category $category
     *
     * @return Publication
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
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

    /**
     * Add genre
     *
     * @param \AppBundle\Entity\Genre $genre
     *
     * @return Publication
     */
    public function addGenre(\AppBundle\Entity\Genre $genre)
    {
        $this->genres[] = $genre;

        return $this;
    }

    /**
     * Remove genre
     *
     * @param \AppBundle\Entity\Genre $genre
     */
    public function removeGenre(\AppBundle\Entity\Genre $genre)
    {
        $this->genres->removeElement($genre);
    }

    /**
     * Get genres
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Set sortableTitle
     *
     * @param string $sortableTitle
     *
     * @return Publication
     */
    public function setSortableTitle($sortableTitle)
    {
        $this->sortableTitle = $sortableTitle;

        return $this;
    }

    /**
     * Get sortableTitle
     *
     * @return string
     */
    public function getSortableTitle()
    {
        return $this->sortableTitle;
    }
    
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateSortableTitle()
    {
        if ($this->sortableTitle === null || $this->sortableTitle === '') {
            $this->sortableTitle = $this->sortableTitle;
        }
    }
}
