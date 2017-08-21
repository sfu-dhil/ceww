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
     * @var String
     * @ORM\Column(type="string", length=48, nullable=true)
     */
    private $runDates;
    
    /**
     * @var String
     * @ORM\Column(type="text", nullable=true)
     */
    private $continuedFrom;
    
    /**
     * @var String
     * @ORM\Column(type="text", nullable=true)
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
