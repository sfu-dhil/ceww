<?php

/*
    * To change this license header, choose License Headers in Project Properties.
    * To change this template file, choose Tools | Templates
    * and open the template in the editor.
 */

namespace AppBundle\Services;

use AppBundle\Entity\Place;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;

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
            foreach ($p->getAuthorsBorn() as $a) {
                $a->setBirthPlace($destination);
                $p->removeAuthorsBorn($a);
            }
            foreach ($p->getAuthorsDied() as $a) {
                $a->setDeathPlace($destination);
                $p->removeAuthorsDied($a);
            }
            foreach ($p->getResidents() as $a) {
                $a->removeResidence($p);
                $a->addResidence($destination);
            }
        }
        foreach ($places as $p) {
            $this->em->remove($p);
        }
        $this->em->flush();
    }

}
