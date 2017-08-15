<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Periodical
 *
 * @ORM\Table(name="periodical")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PeriodicalRepository")
 */
class Periodical extends Publication
{
    /**
     * @var Place
     * @ORM\ManyToOne(targetEntity="Place", inversedBy="peopleBorn")
     */
    private $location;

    /**
     * @var String
     * @ORM\Column(type="string", length=24)
     */
    private $runDates;
    
    /**
     * @var String
     * @ORM\Column(type="string", length=64)
     */
    private $continuedFrom;
    
    /**
     * @var String
     * @ORM\Column(type="string", length=64)
     */
    private $continuedBy;
    
    /**
     * Set runDates
     *
     * @param string $runDates
     *
     * @return Periodical
     */
    public function setRunDates($runDates)
    {
        $this->runDates = $runDates;

        return $this;
    }

    /**
     * Get runDates
     *
     * @return string
     */
    public function getRunDates()
    {
        return $this->runDates;
    }

    /**
     * Set continuedFrom
     *
     * @param string $continuedFrom
     *
     * @return Periodical
     */
    public function setContinuedFrom($continuedFrom)
    {
        $this->continuedFrom = $continuedFrom;

        return $this;
    }

    /**
     * Get continuedFrom
     *
     * @return string
     */
    public function getContinuedFrom()
    {
        return $this->continuedFrom;
    }

    /**
     * Set continuedBy
     *
     * @param string $continuedBy
     *
     * @return Periodical
     */
    public function setContinuedBy($continuedBy)
    {
        $this->continuedBy = $continuedBy;

        return $this;
    }

    /**
     * Get continuedBy
     *
     * @return string
     */
    public function getContinuedBy()
    {
        return $this->continuedBy;
    }
}
