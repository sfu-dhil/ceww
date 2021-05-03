<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Services;

use App\Entity\Contribution;
use App\Entity\Periodical;
use App\Entity\Place;
use App\Entity\Publisher;
use App\Repository\PlaceRepository;
use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Description of PlaceMerger.
 *
 * @author mjoyce
 */
class Merger {
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
     * @var PlaceRepository
     */
    private $placeRepository;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $em) {
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
     * @required
     */
    public function setPlaceRepository(PlaceRepository $placeRepository) : void {
        $this->placeRepository = $placeRepository;
    }

    /**
     * @param Place[]
     * @param mixed $placeIds
     */
    public function getPlaces($placeIds) {
        return $this->placeRepository->findBy(['id' => $placeIds]);
    }

    /**
     * @param Place[] $places
     */
    public function places(Place $destination, $places) : void {
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

            foreach ($p->getPublications() as $a) {
                $a->setLocation($destination);
            }

            foreach ($p->getPublishers() as $publisher) {
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
     * @param Periodical[] $publications
     */
    public function periodicals(Periodical $destination, array $publications) : void {
        foreach ($publications as $publication) {
            foreach ($publication->getLinks() as $link) {
                $destination->addLink($link);
            }
            $destination->appendNote($publication->getNotes());

            foreach ($publication->getGenres() as $genre) {
                $destination->addGenre($genre);
            }

            foreach ($publication->getContributions() as $contribution) {
                $replacement = new Contribution();
                $replacement->setPublication($destination);
                $replacement->setRole($contribution->getRole());
                $replacement->setPerson($contribution->getPerson());
                $this->em->persist($replacement);
                $this->em->remove($contribution);
            }

            foreach ($publication->getPublishers() as $publisher) {
                $publication->removePublisher($publisher);
                $destination->addPublisher($publisher);
            }
            $this->em->remove($publication);
        }
        $this->em->flush();
    }

    /**
     * @param Publisher[] $publishers
     */
    public function publishers(Publisher $destination, array $publishers) : void {
        foreach ($publishers as $publisher) {
            foreach ($publisher->getPlaces() as $place) {
                $destination->addPlace($place);
            }

            foreach ($publisher->getPublications() as $publication) {
                $destination->addPublication($publication);
            }
            $notes = trim($destination->getNotes() . "\n" . $publisher->getNotes());
            $destination->setNotes(nl2br($notes));
            $this->em->remove($publisher);
        }
        $this->em->flush();
    }
}
