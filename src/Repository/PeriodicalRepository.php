<?php

namespace App\Repository;

use App\Entity\Periodical;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * PeriodicalRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PeriodicalRepository extends PublicationRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Periodical::class);
    }

    public function findPeriodical($title, $placeName = null) {
        $qb = $this->createQueryBuilder('p');
        $qb->andWhere('p.title = :title');
        $qb->setParameter('title', $title);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            throw new Exception("Duplicate publication detected:{$e->getMessage()} - " . implode(':', array('periodical', $title, $placeName)));
        }
    }
}