<?php

declare(strict_types=1);

/*
 * (c) 2022 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Book;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * BookRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BookRepository extends PublicationRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Book::class);
    }

    public function findBook($title, $date = null, $placeName = null) {
        $qb = $this->createQueryBuilder('p');
        $qb->andWhere('p.title = :title');
        $qb->setParameter('title', $title);

        if ($date) {
            $qb->innerJoin('p.dateYear', 'd');
            $qb->andWhere('d.value = :value');
            $qb->setParameter('value', $date);
        } else {
            $qb->andWhere('p.dateYear is null');
        }

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
            throw new Exception('Duplicate publication detected - ' . implode(':', ['book', $title, $date, $placeName]));
        }
    }
}
