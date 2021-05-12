<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Exception;
use Nines\UtilBundle\Entity\AbstractEntity;

define('CIRCA_RE', '(c?)([1-9][0-9]{3})');
define('YEAR_RE', '/^' . CIRCA_RE . '$/');
define('RANGE_RE', '/^(?:' . CIRCA_RE . ')?-(?:' . CIRCA_RE . ')?$/');

/**
 * Date.
 *
 * @ORM\Table(name="date_year", indexes={
 *     @ORM\Index(columns="start"),
 *     @ORM\Index(columns="end")
 * })
 * @ORM\Entity(repositoryClass="App\Repository\DateYearRepository")
 */
class DateYear extends AbstractEntity {
    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $value;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $start;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private $startCirca;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $end;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private $endCirca;

    public function __construct() {
        parent::__construct();
        $this->start = null;
        $this->startCirca = false;
        $this->end = null;
        $this->endCirca = false;
    }

    /**
     * Return a string representation.
     */
    public function __toString() : string {
        if (($this->startCirca === $this->endCirca) && ($this->start === $this->end)) {
            return ($this->startCirca ? 'c' : '') . $this->start;
        }

        return ($this->startCirca ? 'c' : '') . $this->start .
                '-' .
                ($this->endCirca ? 'c' : '') . $this->end;
    }

    public function getValue() {
        return (string) $this;
    }

    public function setValue($value) {
        $this->value = $value;
        $value = mb_strtolower(preg_replace('/\s*/', '', (string) $value));
        $matches = [];
        if (false === mb_strpos($value, '-')) {
            // not a range
            if (preg_match(YEAR_RE, $value, $matches)) {
                $this->startCirca = ('c' === $matches[1]);
                $this->start = $matches[2];
                $this->endCirca = $this->startCirca;
                $this->end = $this->start;
            } else {
                throw new Exception("Malformed date:  '{$value}'");
            }

            return $this;
        }
        if ( ! preg_match(RANGE_RE, $value, $matches)) {
            throw new Exception("Malformed Date range '{$value}'");
        }

        $this->startCirca = ('c' === $matches[1]);
        $this->start = $matches[2];
        if (count($matches) > 3) {
            $this->endCirca = ('c' === $matches[3]);
            $this->end = $matches[4];
        }

        return $this;
    }

    public function isRange() {
        return
            ($this->startCirca !== $this->endCirca)
            || ($this->start !== $this->end);
    }

    public function hasStart() {
        return null !== $this->start && '' !== $this->start;
    }

    /**
     * Get start.
     *
     * @param mixed $withCirca
     *
     * @return int
     */
    public function getStart($withCirca = true) {
        if ($withCirca && $this->startCirca) {
            return 'c' . $this->start;
        }

        return $this->start ?? '';
    }

    public function hasEnd() {
        return null !== $this->end && '' !== $this->end;
    }

    /**
     * Get end.
     *
     * @param mixed $withCirca
     *
     * @return int
     */
    public function getEnd($withCirca = true) {
        if ($withCirca && $this->endCirca) {
            return 'c' . $this->end;
        }

        return $this->end ?? '';
    }

    /**
     * Set start.
     *
     * @param int $start
     *
     * @return DateYear
     */
    public function setStart($start) {
        $this->start = $start;

        return $this;
    }

    /**
     * Set startCirca.
     *
     * @param bool $startCirca
     *
     * @return DateYear
     */
    public function setStartCirca($startCirca) {
        $this->startCirca = $startCirca;

        return $this;
    }

    /**
     * Get startCirca.
     *
     * @return bool
     */
    public function getStartCirca() {
        return $this->startCirca;
    }

    /**
     * Set end.
     *
     * @param int $end
     *
     * @return DateYear
     */
    public function setEnd($end) {
        $this->end = $end;

        return $this;
    }

    /**
     * Set endCirca.
     *
     * @param bool $endCirca
     *
     * @return DateYear
     */
    public function setEndCirca($endCirca) {
        $this->endCirca = $endCirca;

        return $this;
    }

    /**
     * Get endCirca.
     *
     * @return bool
     */
    public function getEndCirca() {
        return $this->endCirca;
    }
}
