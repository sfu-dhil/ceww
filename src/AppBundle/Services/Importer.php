<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Services;

use AppBundle\Entity\Alias;
use AppBundle\Entity\Author;
use AppBundle\Entity\Category;
use AppBundle\Entity\Place;
use AppBundle\Entity\Publication;
use AppBundle\Utilities\TitleCaser;
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
     * @var TitleCaser
     */
    private $titleCaser;

    public function __construct() {
        $this->titleCaser = new TitleCaser();
    }

    /**
     * Set the service logger.
     *
     * @param Logger $logger
     */
    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }

    /**
     * Set the ORM.
     *
     * @param Registry $registry
     */
    public function setDoctrine(Registry $registry) {
        $this->em = $registry->getManager();
    }

    public function split($s, $delim = ';') {
        $result = mb_split($delim, $s);
        return array_filter(array_map(function($value) {
            return preg_replace('/^\p{Z}+|\p{Z}+$/u', '', $value);
        }, $result));
    }

    public function processName($string) {
        list($family, $given) = preg_split('/,\s*/u', $string);
        return "{$given} {$family}";
    }

    public function sortableName($string) {
        return mb_convert_case($string, MB_CASE_LOWER);
    }

    public function processDate($string) {
        $matches = array();
        if (preg_match('/^(c?\d{4})$/u', $string, $matches)) {
            // exactly a year.
            return $matches[1];
        }

        if (preg_match('/^(c?\d{4})-(c?\d{4})$/', $string, $matches)) {
            // exactly a range of years.
            return array($matches[1], $matches[2]);
        }

        if (preg_match('/(c?\d{4})/u', $string, $matches)) {
            // a year, anywhere.
            return $matches[1];
        }

        if (preg_match('/(\d{2})/', $string, $matches)) {
            // two digit year? Can't be a circa.
            return $matches[1] + 1900;
        }
        return null;
    }

    public function processPlace($string) {
        $s = preg_replace('/\bnear\s+/ui', '', $string);
        $matches = array();
        if (!preg_match('/^(.+)\((.*)\)/', $s, $matches)) {
            return array($s, null);
        }
        $placeName = $matches[1];
        $note = $matches[2];
        if (preg_match('/[^0-9, c-]+/', $note)) {
            return array($placeName, $note);
        }
        return array($placeName, null);
    }

    public function getPlace($placeName, $notes = '') {
        $repo = $this->em->getRepository('AppBundle:Place');
        $place = $repo->findOneBy(array(
            'name' => $placeName,
        ));
        if (!$place) {
            $place = new Place();
            $place->setName($placeName);
            $this->em->persist($place);
            $this->em->flush($place);
            $this->em->clear('AppBundle:Place');
        }
        if ($notes && strpos($place->getDescription(), $notes) === false) {
            $place->setDescription($place->getDescription() . "\n" . $notes);
            $this->em->flush($place);
        }
        return $place;
    }
    
    public function getAlias($name) {
        $repo = $this->em->getRepository('AppBundle:Alias');
        $alias = $repo->findOneBy(array(
            'name' => $name,
        ));
        if( ! $alias) {
            $alias = new Alias();
            $alias->setName($name);
            $alias->setMaiden(preg_match('/\bn(?:é|e)e\b/u', $name));
            $this->em->persist($alias);
            $this->em->flush($alias);
            $this->em->clear('AppBundle:Alias');
        }
        return $alias;
    }

    /**
        'linked_title' => '',
        'url' => '',
        'title' => 'Wanted, a Wife',
        'loc' => '',
        'year' => '',
        'genre' => ''
     * 
     * @param string $string
     * @return array
     */
    public function processTitle($string) {
        $matches = array();
        $genre_re = '(?:\{(?P<genre>\w+)\})';
        $year_re = '(?P<year>c?(?:\d{4})|(?:n\.d\.)?)';
        $loc_re = '(?:(?P<loc>.*), )?';
        $pub_re = '(?:\(' . $loc_re . $year_re . '\))';
        
        $unlinked_re = '(?P<title>.*?)';
        $linked_re = "\<(?P<linked_title>.*?)\p{Z}(?P<url>https?:\/\/.*?)\s*\>";
        $title = "(?:(?:$linked_re)|(?:$unlinked_re))";
        $re = "$title\p{Z}*$pub_re?\p{Z}*$genre_re?";
        preg_match("/^$re$/iu", $string, $matches);
        
        print "\n\n\n" . $re . "\n\n\n";
        
        return $matches;
    }

    public function sortableTitle($string) {
        $filters = array(
            '/^(the|an?)\b\s*(.*)/ius' => '$2, $1',
            // move The, A, An to end.
            '/^[^[:word:][:space:]]+/us' => '',
            // remove non-word chars at start.
        );
        $title = mb_convert_case($string, MB_CASE_LOWER);
        foreach($filters as $pattern => $replacement) {
            $title = preg_replace($pattern, $replacement, $title);
        }
        return $title;
    }

    public function getPublication($string, Category $category) {
        $pubData = $this->processTitle($string);
        $url = null;
        $title = $pubData['title'];
        $year = null;
        if(array_key_exists('year', $pubData) && $pubData['year'] !== 'n.d.') {
            $year = $pubData['year'];
        }
        if($pubData['linked_title'] && $pubData['url']) {
            $title = $pubData['linked_title'];
            $url = $pubData['url'];
        }
        
        $repo = $this->em->getRepository('AppBundle:Publication');
        $publication = $repo->findOneBy(array(
            'title' => $title,
            'year' => $year,
        ));
        if( ! $publication) {
            $publication = new Publication();
            $publication->setTitle($this->titleCaser->titlecase($title));
            $publication->setSortableTitle($this->sortableTitle($title));
            $publication->setYear($year);
            if(array_key_exists('loc', $pubData) && $pubData['loc']) {
                $publication->setLocation($pubData['loc']);
            }
            $publication->setCategory($category);
            $this->em->persist($publication);
        }
        
        if($url) {
            $publication->addLink($url);
        }
        $this->em->flush($publication);
        return $publication;
    }

    public function import(array $row) {
//        $author = new Author();
//        $author->setFullName($this->processName($row[0]));
//        $author->setSortableName($this->sortableName($row[0]));
//        $birthDate = $this->processDate($row[1]);
//        if (is_array($birthDate)) {
//            $author->setBirthDate($birthDate[0]);
//            $author->setDeathDate($birthDate[1]);
//        } else {
//            $author->setBirthDate($birthDate);
//            $author->setDeathDate($this->processDate($row[3]));
//        }
//        if ($author->getBirthDate() && $author->getDeathDate() &&
//            $author->getDeathDate() < $author->getBirthDate()) {
//            $this->logger->warning('Died before Birth');
//        }
//
//        list($birthPlaceName, $birthPlaceNotes) = $this->processPlace($row[2]);
//        $birthPlace = $this->getPlace($birthPlaceName, $birthPlaceNotes);
//        $author->setBirthPlace($birthPlace);
//
//        list($deathPlaceName, $deathPlaceNotes) = $this->processPlace($row[4]);
//        $deathPlace = $this->getPlace($deathPlaceName, $deathPlaceNotes);
//        $author->setDeathPlace($deathPlace);
//        
//        $aliases = $this->split($row[5]);
//        foreach($aliases as $name) {
//            $alias = $this->getAlias($name);
//            $author->addAlias($alias);
//        }
//
//        $residenceNames = $this->split($row[6]);
//        foreach ($residenceNames as $name) {
//            list($placeName, $desc) = $this->processPlace($name);
//            $place = $this->getPlace($placeName, $desc);
//            $author->addResidence($place);
//        }
        $books = $this->split($row[7]);
        $bookCategory = $this->em->getRepository('AppBundle:Category')->findOneBy(array(
            'label' => 'Book',
        ));
        foreach($books as $book) {
            $publication = $this->getPublication($book, $bookCategory);
        }
        // dump($author);
    }

}
