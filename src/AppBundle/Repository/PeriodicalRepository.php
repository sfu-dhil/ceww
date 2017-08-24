<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Exception;

/**
 * PeriodicalRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PeriodicalRepository extends EntityRepository
{
    public function findPeriodical($title, $placeName = null) {
        $qb = $this->createQueryBuilder('p');
        $qb->andWhere('p.title = :title');
        $qb->setParameter('title', $title);

        if ($placeName) {
            $qb->innerJoin('p.location', 'l');
            $qb->andWhere('l.name = :place');
            $qb->setParameter('place', $placeName);
        } else {
            $qb->andWhere('p.location is null');
        }

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            throw new Exception("Duplicate publication detected:{$e->getMessage()} - " . implode(':', ['periodical', $title, $placeName]));
        }
    }
}
