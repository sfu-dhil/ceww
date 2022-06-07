<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Person;
use App\Entity\Publisher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * PublisherRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PublisherRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Publisher::class);
    }

    public function typeaheadQuery($q) {
        $qb = $this->createQueryBuilder('e');
        $qb->andWhere('e.name LIKE :q');
        $qb->orderBy('e.name');
        $qb->setParameter('q', "%{$q}%");

        return $qb->getQuery()->execute();
    }

    public function searchQuery($q) {
        $qb = $this->createQueryBuilder('e');
        $qb->addSelect('MATCH (e.name) AGAINST (:q BOOLEAN) as HIDDEN score');
        $qb->add('where', 'MATCH (e.name) AGAINST (:q BOOLEAN) > 0.0');
        $qb->orderBy('score', 'desc');
        $qb->setParameter('q', $q);

        return $qb->getQuery();
    }

    public function byPerson(Person $person) {
        $qb = $this->createQueryBuilder('pb');
        $qb->distinct();
        $qb->join('pb.publications', 't'); // t for title.
        $qb->join('t.contributions', 'c');
        $qb->andWhere('c.person = :person');
        $qb->setParameter('person', $person);
        $qb->orderBy('pb.name');

        return $qb->getQuery()->execute();
    }
}
