<?php

/*
    * To change this license header, choose License Headers in Project Properties.
    * To change this template file, choose Tools | Templates
    * and open the template in the editor.
 */

namespace AppBundle\Services;

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

}
