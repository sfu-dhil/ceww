<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Services;

use AppBundle\Entity\Periodical;
use AppBundle\Entity\Person;
use AppBundle\Entity\Place;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Monolog\Logger;
use Nines\UtilBundle\Services\TitleCaser;
use ReflectionClass;



/**
 * Description of Importer
 *
 * @author mjoyce
 */
class PeriodicalImporter {
    
    /**
     * @var ObjectManager
     */
    private $em;
    
    /**
     * @var TitleCaser
     */
    private $titleCaser;
    
    /**
     * @var Namer
     */
    private $namer;
    
    /**
     * @var Logger
     */
    private $logger;
    
    /**
     * @var boolean
     */
    private $commit;
    
    public function __construct() {
        $this->namer = new Namer();
        $this->commit = false;
    }
    
    public function setDoctrine(Registry $doctrine) {
        $this->em = $doctrine->getManager();
    }
    
    public function setTitleCaser(TitleCaser $titleCaser) {
        $this->titleCaser = $titleCaser;
    }
    
    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }
    
    public function setCommit($commit) {
        $this->commit = $commit;
    }

    public function persist($entity) {
        $reflection = new ReflectionClass($entity);
        $this->logger->notice($reflection->getShortName());
        $this->logger->info($entity);
        if ($this->commit) {
            $this->em->persist($entity);
            $this->flush($entity);
        }
    }

    public function flush($entity = null, $clear = false) {
        if ($this->commit) {
            $this->em->flush($entity);
            if ($clear) {
                $this->em->clear();
                gc_collect_cycles();
            }
        }
    }

    public function trim($s) {
        return preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $s);
    }

    public function split($s, $delim = ';') {
        $result = mb_split($delim, $s);
        return array_filter(array_map(function($value) {
                    return $this->trim($value);
                }, $result));
    }

    public function getPlace($value) {
        $name = $this->trim(preg_replace('/\([^)]*\)/u', '', $value));
        if( ! $name) {
            return null;
        }
        $repo = $this->em->getRepository(Place::class);        
        $place = $repo->findOneBy(array('name' => $name));
        if (!$place) {
            $place = new Place();
            $place->setName($name);
            $data = preg_split('/\s*,\s*/u', $name, 3);
            if(count($data) > 1) {
                $place->setRegionName($data[1]);
            }
            if(count($data) > 2) {
                $place->setCountryName($data[2]);
            }
            $sortable = mb_convert_case($name, MB_CASE_LOWER, 'UTF-8');
            $sortable = preg_replace('/^[^a-z -]*/', '', $sortable);
            $place->setSortableName($sortable);
            $this->persist($place);
        }
        return $place;
    }

    public function titleDate($title) {
        $matches = array();
        if (preg_match('/^(.*?)\(n\.d\.\)\s*$/u', $title, $matches)) {
            return array($this->trim($matches[1]), null);
        }
        if (preg_match('/^(.*?)\[(c?\d{4}(?:,\s*c?\d{4})*)\]\s*$/u', $title, $matches)) {
            return array($this->trim($matches[1]), $matches[2]);
        }
        if (preg_match('/^(.*?)\((c?\d{4}(?:,\s*c?\d{4})*)\)\s*$/u', $title, $matches)) {
            return array($this->trim($matches[1]), $matches[2]);
        }
        if (preg_match('/^(.*?)\(\[(c?\d{4}(?:,\s*c?\d{4})*)\]\)\s*$/u', $title, $matches)) {
            return array($this->trim($matches[1]), $matches[2]);
        }
        return array($title, null);
    }
    
    public function titlePlace($title) {
        $matches = array();
        if (preg_match('/^(.*?)\(([^)]*)\)\s*$/u', $title, $matches)) {
            return array($this->trim($matches[1]), $matches[2]);
        }
        return array($title, null);
    }
    
    public function getPeriodical($title) {
        $periodicalRepo = $this->em->getRepository(Periodical::class);
        $periodical = $periodicalRepo->findOneBy(array(
            'title' => $title
        ));
        if( ! $periodical) {
            $periodical = new Periodical();
        }
        return $periodical;
    }

    /**
     * @return Person
     */
    public function importRow($row) {
        $periodical = $this->getPeriodical($row[0]);
        $periodical->setTitle($row[0]);
        $periodical->setSortableTitle($this->titleCaser->sortableTitle($row[0]));
        $periodical->setLocation($this->getPlace($row[1]));
        $periodical->setRunDates($row[2]);
        $periodical->setContinuedFrom($row[3]);
        $periodical->setContinuedBy($row[4]);
        $periodical->setNotes($row[5]);
        $this->em->persist($periodical);
        $this->flush(null, true);
        return $periodical;
    }

}
