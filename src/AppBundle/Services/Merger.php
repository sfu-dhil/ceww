<?php

/*
    * To change this license header, choose License Headers in Project Properties.
    * To change this template file, choose Tools | Templates
    * and open the template in the editor.
 */

namespace AppBundle\Services;

use AppBundle\Entity\Contribution;
use AppBundle\Entity\Periodical;
use AppBundle\Entity\Place;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Description of PlaceMerger
 *
 * @author mjoyce
 */
class Merger
{
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

    public function __construct(LoggerInterface $logger, EntityManagerInterface $em) {
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
     * @param Place[]
     */
    public function getPlaces($placeIds) {
        $repo = $this->em->getRepository('AppBundle:Place');
        return $repo->findBy(array('id' => $placeIds));
    }

    /**
     * @param Place   $destination
     * @param Place[] $places
     */
    public function places(Place $destination, $places) {
        foreach ($places as $p) {
            foreach ($p->getPeopleBorn() as $a) {
                $a->setBirthPlace($destination);
                $p->removePersonBorn($a);
            }
            foreach ($p->getPeopleDied() as $a) {
                $a->setDeathPlace($destination);
                $p->removePersonDied($a);
            }
            foreach ($p->getResidents() as $a) {
                $a->removeResidence($p);
                $a->addResidence($destination);
            }
            foreach($p->getPublications() as $a) {
                $a->setLocation($destination);
            }
            foreach($p->getPublishers() as $publisher) {
                $publisher->removePlace($p);
                $publisher->addPlace($destination);
            }
        }
        foreach ($places as $p) {
            $this->em->remove($p);
        }
        $this->em->flush();
    }

    /**
     * Merge $publications data into $destination and remove $publications.
     *
     * @todo this is generic enough that it can apply to publications, not just
     * periodicals.
     *
     * @param Periodical   $destination
     * @param Periodical[] $publications
     */
    public function periodicals(Periodical $destination, array $publications) {
        foreach($publications as $publication) {
            foreach($publication->getLinks() as $link) {
                $destination->addLink($link);
            }
            $destination->appendNote($publication->getNotes());
            foreach($publication->getGenres() as $genre) {
                $destination->addGenre($genre);
            }
            foreach($publication->getContributions() as $contribution) {
                $replacement = new Contribution();
                $replacement->setPublication($destination);
                $replacement->setRole($contribution->getRole());
                $replacement->setPerson($contribution->getPerson());
                $this->em->persist($replacement);
                $this->em->remove($contribution);
            }
            foreach($publication->getPublishers() as $publisher) {
                $publication->removePublisher($publisher);
                $destination->addPublisher($publisher);
            }
            $this->em->remove($publication);
        }
        $this->em->flush();
    }

}
