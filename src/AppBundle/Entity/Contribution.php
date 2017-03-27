<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Contribution
 *
 * @ORM\Table(name="contribution")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContributionRepository")
 */
class Contribution extends AbstractEntity
{

    /**
     * @var Role
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="contributions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $role;
    
    /**
     * @var Person
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="contributions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $person;
    
    /**
     * @var Publication
     * @ORM\ManyToOne(targetEntity="Publication", inversedBy="contributions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $publication;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function __toString() {
        return "{$this->role->getName()}:{$this->person->getFullName()}:{$this->publication->getTitle()}";
    }

    /**
     * Set role
     *
     * @param Role $role
     *
     * @return Contribution
     */
    public function setRole(Role $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set person
     *
     * @param Person $person
     *
     * @return Contribution
     */
    public function setPerson(Person $person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set publication
     *
     * @param Publication $publication
     *
     * @return Contribution
     */
    public function setPublication(Publication $publication)
    {
        $this->publication = $publication;

        return $this;
    }

    /**
     * Get publication
     *
     * @return Publication
     */
    public function getPublication()
    {
        return $this->publication;
    }
}
