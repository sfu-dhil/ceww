<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Services;

use AppBundle\Entity\Alias;
use AppBundle\Entity\Book;
use AppBundle\Entity\Compilation;
use AppBundle\Entity\Contribution;
use AppBundle\Entity\DateYear;
use AppBundle\Entity\Periodical;
use AppBundle\Entity\Person;
use AppBundle\Entity\Place;
use AppBundle\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Monolog\Logger;
use Nines\UtilBundle\Services\TitleCaser;
use ReflectionClass;

/**
 * Description of Importer
 *
 * @author mjoyce
 */
class AuthorImporter {

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
    
    /**
     * @var Splitter
     */
    private $splitter;
    
    private $rowCount = 0;
    
    private $source;

    public function __construct() {
        $this->namer = new Namer();
        $this->commit = false;
    }
    
    public function setSource($source) {
        $this->source = $source;
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
    
    public function setSplitter(Splitter $splitter) {
        $this->splitter = $splitter;
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

    public function createPerson($name) {
        $person = new Person();
        if ($name) {
            $person->setFullName($this->namer->lastFirstToFull($name));
            $person->setSortableName($this->namer->sortableName($name));
        } else {
            $person->setFullname('');
            $person->setSortableName('');
        }
        $person->setGender(Person::FEMALE);
        $this->persist($person);
        return $person;
    }

    public function getPlace($value) {
        $name = $this->trim(preg_replace('/\([^)]*\)/u', '', $value));
        if (!$name) {
            return null;
        }
        $repo = $this->em->getRepository(Place::class);
        $place = $repo->findOneBy(array('name' => $name));
        if (!$place) {
            $place = new Place();
            $place->setName($name);
            $sortable = mb_convert_case($name, MB_CASE_LOWER, 'UTF-8');
            $sortable = preg_replace('/^[^a-z]*/', '', $sortable);
            $place->setSortableName($sortable);
            $this->persist($place);
        }
        return $place;
    }

    public function getDateYear($date) {
        try {
            if ($date) {
                $dateYear = new DateYear();
                $dateYear->setValue($date);
                $this->persist($dateYear);
                return $dateYear;
            }
        } catch (Exception $e) {
            $trace = debug_backtrace(null, 2);
            $caller = $trace[1];
            $this->logger->error($caller['function'] . ':' . $e->getMessage());
        }
        return null;
    }

    public function setBirthDate(Person $person, $value) {
        $value = $this->trim($value);
        if (!$value) {
            return;
        }
        $birthDate = $this->getDateYear($value);
        $person->setBirthDate($birthDate);
    }

    public function setBirthPlace(Person $person, $value) {
        $birthPlace = $this->getPlace($value);
        if (!$birthPlace) {
            return;
        }
        $person->setBirthPlace($birthPlace);
        $birthPlace->addPersonBorn($person);
    }

    public function setDeathDate(Person $person, $value) {
        $value = $this->trim($value);
        if (!$value) {
            return;
        }        
        $deathDate = $this->getDateYear($value);
        $person->setDeathDate($deathDate);
    }

    public function setDeathPlace(Person $person, $value) {
        $deathPlace = $this->getPlace($value);
        if (!$deathPlace) {
            return;
        }
        $person->setDeathPlace($deathPlace);
        $deathPlace->addPersonBorn($person);
    }

    public function getAlias($name) {
        $repo = $this->em->getRepository(Alias::class);
        $alias = $repo->findOneBy(array('name' => $name));
        if (!$alias) {
            $alias = new Alias();
            $alias->setMaiden(preg_match('/^n(é|e)e\s+/u', $name));
            if ($alias->getMaiden()) {
                $alias->setName($this->trim(substr($name, 4)));
            } else {
                $alias->setName($name);
            }
            $this->persist($alias);
        }
        return $alias;
    }

    public function addAliases(Person $person, $value) {
        $names = $this->splitter->split($value);
        foreach ($names as $name) {
            $alias = $this->getAlias($name);
            $person->addAlias($alias);
        }
        if ($person->getFullName() === '(unknown)') {
            $alias = $person->getAliases()->first();
            if ($alias) {
                $person->setSortableName($this->namer->sortableName($this->namer->fullToLastFirst($alias->getName())));
            }
        }
    }

    public function addResidences(Person $person, $value) {
        $names = $this->splitter->split($value);
        foreach ($names as $name) {
            $place = $this->getPlace($name);
            if( ! $place) {
                continue;
            }
            $person->addResidence($place);
            $place->addResident($person);
        }
    }

    public function titleDate($title) {
        $matches = array();
        if (preg_match('/^(.*?)\(n\.d\.\)\s*$/u', $title, $matches)) {
            return array($this->trim($matches[1]), null);
        }
        if (preg_match('/^(.*?)\[(c?\d{4}(?:[,;-]\s*c?\d{1,4})*)\]\s*$/u', $title, $matches)) {
            return array($this->trim($matches[1]), $matches[2]);
        }
        if (preg_match('/^(.*?)\((c?\d{4}(?:[,;-]\s*c?\d{1,4})*)\)\s*$/u', $title, $matches)) {
            return array($this->trim($matches[1]), $matches[2]);
        }
        if (preg_match('/^(.*?)\(\[(c?\d{4}(?:[,;-]\s*c?\d{1,4})*)\]\)\s*$/u', $title, $matches)) {
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

    public function getBook($title, $date, $placeName) {
        $repo = $this->em->getRepository(Book::class);
        $book = $repo->findBook($title, $date, $placeName);
        if (!$book) {
            $book = new Book();
            $book->setTitle($this->titleCaser->titlecase($title));
            $book->setSortableTitle($this->titleCaser->sortableTitle($title));
            $book->setDateYear($this->getDateYear($date, $book));

            if ($placeName) {
                $place = $this->getPlace($placeName);
                $book->setLocation($place);
                $place->addPublication($book);
            }
            $this->persist($book);
        }
        return $book;
    }

    public function getCompilation($title, $date, $placeName) {
        $repo = $this->em->getRepository(Compilation::class);
        $compilation = $repo->findCompilation($title, $date, $placeName);
        if (!$compilation) {
            $compilation = new Compilation();
            $compilation->setTitle($this->titleCaser->titlecase($title));
            $compilation->setSortableTitle($this->titleCaser->sortableTitle($title));
            $compilation->setDateYear($this->getDateYear($date));

            if ($placeName) {
                $place = $this->getPlace($placeName);
                $compilation->setLocation($place);
                $place->addPublication($compilation);
            }
            $this->persist($compilation);
        }
        return $compilation;
    }

    public function getPeriodical($title) {
        $title = $this->titleCaser->titlecase($title);
        $repo = $this->em->getRepository(Periodical::class);
        $periodical = $repo->findPeriodical($title);
        if (!$periodical) {
            $periodical = new Periodical();
            $periodical->setTitle($title);
            $periodical->setSortableTitle($this->titleCaser->sortableTitle($title));
            $this->persist($periodical);    
        }
        return $periodical;
    }

    public function addPeriodicals(Person $person, $value) {
        $titles = $this->splitter->split($value);
        $roleRepo = $this->em->getRepository(Role::class);
        $role = $roleRepo->findOneBy(array('name' => 'author'));
        foreach ($titles as $title) {
            list($title, $dateValue) = $this->titleDate($title);
            list($title, $placeValue) = $this->titlePlace($title);
            $title = $this->trim($title);

            $publication = $this->getPeriodical($title, $dateValue, $placeValue);
            $contribution = new Contribution();
            $contribution->setPerson($person);
            $contribution->setRole($role);
            $contribution->setPublication($publication);
            $person->addContribution($contribution);
            $this->persist($contribution);
            // $this->em->clear(Periodical::class);
        }
    }

    public function addCompilations(Person $person, $value) {
        $titles = $this->splitter->split($value);
        $roleRepo = $this->em->getRepository(Role::class);
        $role = $roleRepo->findOneBy(array('name' => 'author'));
        foreach ($titles as $title) {
            list($title, $dateValue) = $this->titleDate($title);
            list($title, $placeValue) = $this->titlePlace($title);
            $title = $this->trim($title);

            $publication = $this->getCompilation($title, $dateValue, $placeValue);
            $contribution = new Contribution();
            $contribution->setPerson($person);
            $contribution->setRole($role);
            $contribution->setPublication($publication);
            $person->addContribution($contribution);
            $this->persist($contribution);
        }
    }

    public function addBooks(Person $person, $value) {
        $titles = $this->splitter->split($value);
        $roleRepo = $this->em->getRepository(Role::class);
        $role = $roleRepo->findOneBy(array('name' => 'author'));
        foreach ($titles as $title) {
            list($title, $dateValue) = $this->titleDate($title);
            list($title, $placeValue) = $this->titlePlace($title);
            $title = $this->trim($title);

            $publication = $this->getBook($title, $dateValue, $placeValue);
            $contribution = new Contribution();
            $contribution->setPerson($person);
            $contribution->setRole($role);
            $contribution->setPublication($publication);
            $person->addContribution($contribution);
            $this->persist($contribution);
        }
    }

    /**
     * @return Person
     */
    public function importRow($row) {
        $this->rowCount++;
        $person = $this->createPerson($row[0]);
        $this->setBirthDate($person, $row[1]);
        $this->setBirthPlace($person, $row[2]);
        $this->setDeathDate($person, $row[3]);
        $this->setDeathPlace($person, $row[4]);
        $this->addAliases($person, $row[5]);
        $this->addResidences($person, $row[6]);
        $this->addBooks($person, $row[7]);
        $this->addCompilations($person, $row[8]);
        $this->addPeriodicals($person, $row[9]);
        if (isset($row[10])) {
            $person->setDescription($row[10]);
        }
        $notes = '';
        foreach(array_filter(array_slice($row, 11)) as $note) {
            $notes .= "<p>{$note}</p>\n";
        }
        $person->setNotes($notes);
        $this->flush(null, true);

        return $person;
    }

}
