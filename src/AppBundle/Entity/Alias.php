<?php

namespace AppBundle\Entity;

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
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $maiden;
    
    /**
     * @ORM\Column(type="string", length=24, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    public function __construct() {
        $this->maiden = false;
    }
    
    public function __toString() {
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
}
