<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Services;

use AppBundle\Entity\Alias;
use AppBundle\Entity\Author;
use AppBundle\Entity\Place;
use AppBundle\Entity\Publication;
use AppBundle\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;

/**
 * Description of Importer
 *
 * @author mjoyce
 */
class Importer {
    /**
     * ORM entity manager.
     *
     * @var EntityManager
     */
    private $em;

    /**
     * Service logger.
     *
     * @var Logger
     */
    private $logger;

    /**
     * Set the service logger.
     *
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Set the ORM.
     *
     * @param Registry $registry
     */
    public function setDoctrine(Registry $registry)
    {
        $this->em = $registry->getManager();
    }
    
    public function processDate($str) {
        if (!$str || ctype_space($str)) {
            return null;
        }
        $matches = array();
        if (preg_match('/(\d{4})-(\d{4})/', $str, $matches)) {
            return array($matches[1], $matches[2]);
        }
        if (preg_match('/^(\d{2})-([a-zA-Z]{3})-(\d{2})$/', $str, $matches)) {
            return $matches[3] + 1900;
        }
        if (preg_match('/^([a-zA-Z]{3})-(\d{2})$/', $str, $matches)) {
            return $matches[2] + 1900;
        }
        if (preg_match('/(\d{4})/', $str, $matches)) {
            return $matches[1];
        }
        $this->logger->warning("Unparseable date: {$str}");
        return null;
    }
    
    public function split($s, $delim = ';', $alternate = null) {
        if ($alternate && substr_count($s, $alternate) > 1 && substr_count($delim, $s) < substr_count($s, $alternate)) {
            $this->logger->warning('Possibly malformed string: ' . $s);
            $a = explode($alternate, $s);
        } else {
            $a = explode($delim, $s);
        }
        for ($i = 0; $i < count($a); $i++) {
            $a[$i] = trim($a[$i]);
        }
        return $a;
    }

    public function cleanPlaceName($placeName) {
        $filters = array(
            '/^"[^"]*"\s*/' => '', // remove quoted place name at start
            '/\s+\([^)]*\)$/' => '', // remove parenthesized location
            '/^\s*near\b\s*/i' => '', // remove "near "
        );
        
        $name = $placeName;
        foreach($filters as $key => $value) {
            $name = preg_replace($key, $value, $name);
        }
        return trim($name);
    }
    
    public function titleCase($title) {
        $cased = ucwords(strtolower(trim($title)));
        // this is a terrible hack.
        if($cased[0] === '"') {
            // embarassing really.
            $cased[1] = strtoupper($cased[1]);
        }
        return $cased;
    }
    
    public function cleanTitle($publicationTitle) {
        $filters = array(
            '/\(c?\d{4}(-c?\d{4})?\)/' => '', // remove year or range
            '/^"([^"]*)"$/' => '$1', // remove front/rear quotes.
        );
        
        $title = $publicationTitle;
        foreach($filters as $key => $value) {
            $title = preg_replace($key, $value, $title);
        }
        return $this->titleCase($title);
    }
    
    public function sortableTitle($cleanTitle) {
        $filters = array(
            '/^(the|an?)\b\s*(.*)/i' => '$2, $1', // move The, A, An to end.
            '/^[^[:word:][:space:]]+/' => '', // remove non-word chars at start.
        );
        
        $title = strtolower($cleanTitle);
        foreach($filters as $key => $value) {
            $title = preg_replace($key, $value, $title);
        }
        return trim($title);
    }
    
    public function createAlias($name) {
        $e = new Alias();
        $e->setMaiden(preg_match('/\bn(?:é|e)e\b/', $name));
        $e->setName($name);
        return $e;
    }
    
    public function addAliases($alternateNames) {
        $aliases = $this->split($alternateNames, ';', ',');
        $repo = $this->em->getRepository('AppBundle:Alias');
        $entities = array();
        foreach ($aliases as $name) {
            $e = $repo->findOneByName($name);
            if (!$e) {
                $e = $this->createAlias($name);
                $this->em->persist($e);
                $this->em->flush($e);
            }
            $entities[] = $e;
        }
        return $entities;
    }
    
    public function createPlace($name) {
        $place = new Place();
        $place->setName($name);
        return $place;
    }
    
    public function addPlaces($placeNames) {
        if ($placeNames === '') {
            return array();
        }
        $names = $this->split($placeNames);
        $repo = $this->em->getRepository('AppBundle:Place');
        $entities = array();
        foreach ($names as $name) {
            $name = $this->cleanPlaceName($name);
            if(! $name || ctype_space($name)) {
                continue;
            }
            $e = $repo->findOneByName($name);
            if ($e === null) {
                $e = $this->createPlace($name);
                $this->em->persist($e);
                $this->em->flush($e);
            }
            $entities[] = $e;
        }
        return $entities;
    }
    
    public function createPublication($title, Category $type) {
        $e = new Publication();
        $e->setCategory($type);
        $e->setTitle($title);
        $sortableTitle = $this->sortableTitle($title);
        $e->setSortableTitle($sortableTitle);
        return $e;
    }
    
    public function findCategory($typeName) {
        $typeRepo = $this->em->getRepository('AppBundle:Category');
        $type = $typeRepo->findOneByLabel($typeName);
        if ($type === null) {
            $this->logger->error("Unknown category " . $typeName);
        }
        return $type;
    }
    
    public function addPublications($titleNames, $typeName) {
        if ($titleNames === '') {
            return array();
        }
        $titles = $this->split($titleNames);
        $type = $this->findCategory($typeName);
        
        $repo = $this->em->getRepository('AppBundle:Publication');
        $entities = array();
        foreach ($titles as $title) {
            $title = $this->cleanTitle($title);
            $e = $repo->findBy(array(
                'category' => $type,
                'title' => $title,
            ));
            if (count($e) === 0) {     
                $e = $this->createPublication($title, $type);
                $this->em->persist($e);
                $this->em->flush($e);
                $entities[] = $e;
            } else {
                $entities[] = $e[0];
            }
        }
        return $entities;
    }
    
    public function setDates(Author $author, $birthDateStr, $deathDateStr) {
        $birthDate = $this->processDate($birthDateStr);
        if($birthDate !== null) {
            if(is_array($birthDate)) {
                $author->setBirthDate($birthDate[0]);
                $author->setDeathDate($birthDate[1]);
            } else {
                $author->setBirthDate($birthDate);
            }
        }

        $deathDate = $this->processDate($deathDateStr);
        if ($deathDate && !is_array($deathDate)) {
            $author->setDeathDate($deathDate);
        }
    }
    
    public function setPlaces(Author $author, $birthPlaceStr, $deathPlaceStr) {
        $birthPlace = $this->addPlaces($birthPlaceStr);
        if (array_key_exists(0, $birthPlace)) {
            $author->setBirthPlace($birthPlace[0]);
        }
        $deathPlace = $this->addPlaces($deathPlaceStr);
        if (array_key_exists(0, $deathPlace)) {
            $author->setDeathPlace($deathPlace[0]);
        }
    }
    
    public function setAliases(Author $author, $aliasStr) {
        $aliases = $this->addAliases($aliasStr);
        foreach($aliases as $alias) {
            $author->addAlias($alias);
        }
    }
    
    public function setResidences(Author $author, $residencesStr) {
        foreach ($this->addPlaces($residencesStr) as $residence) {
            $author->addResidence($residence);
        }
    }
    
    public function setPublications(Author $author, $publicationStr, $type) {
        foreach($this->addPublications($publicationStr, $type) as $publication) {
            $author->addPublication($publication);
        }
    }
    
    public function setNotes(Author $author, $notes = array()) {
        $author->setNotes(trim(implode('\n\n', $notes)));
    }
    
    public function importArray($row = array()) {
        $author = new Author();
        $author->setFullname($row[0]); // sets sortable name as well.
        $this->setDates($author, $row[2], $row[4]);
        $this->setPlaces($author, $row[3], $row[5]);
        $this->setAliases($author, $row[6]);
        $this->setResidences($author, $row[7]);
        $this->setPublications($author, $row[8], 'Book');
        $this->setPublications($author, $row[9], 'Anthology');
        $this->setPublications($author, $row[10], 'Periodical');
        $this->setNotes($author, array_slice($row, 11));
        $status = $this->em->getRepository('AppBundle:Status')->findOneByLabel('Draft');
        $author->setStatus($status);
        $this->em->persist($author);
        $this->em->flush($author);
    }
}
