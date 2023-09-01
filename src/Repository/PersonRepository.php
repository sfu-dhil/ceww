<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Person;
use App\Entity\Publisher;
use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * PersonRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PersonRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Person::class);
    }

    public function next(Person $person) {
        $qb = $this->createQueryBuilder('e');
        $qb->andWhere('e.sortableName > :q');
        $qb->andWhere("e.gender = 'f'");
        $qb->setParameter('q', $person->getSortableName());
        $qb->addOrderBy('e.sortableName', 'ASC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function previous(Person $person) {
        $qb = $this->createQueryBuilder('e');
        $qb->andWhere('e.sortableName < :q');
        $qb->setParameter('q', $person->getSortableName());
        $qb->addOrderBy('e.sortableName', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function pageInfoQuery($q, $pageSize) {
        $start = ($q - 1) * $pageSize;
        $fb = $this->createQueryBuilder('e');
        $fb->where("e.gender <> 'm'");
        $fb->orderBy('e.sortableName');
        $fb->setMaxResults(1);
        $fb->setFirstResult($start);
        $first = $fb->getQuery()->getOneOrNullResult();

        $cb = $this->createQueryBuilder('u');
        $cb->select($cb->expr()->count('u'));
        $cb->where("u.gender <> 'm'");
        $count = $cb->getQuery()->getSingleScalarResult();

        $end = $q * $pageSize - 1;
        $lb = $this->createQueryBuilder('e');
        $lb->where("e.gender <> 'm'");
        $lb->orderBy('e.sortableName');
        $lb->setMaxResults(1);
        $lb->setFirstResult($end);
        $last = $lb->getQuery()->getOneOrNullResult();

        return [
            'first' => $first,
            'last' => $last,
            'total' => $count,
        ];
    }

    public function typeaheadQuery($q) {
        $qb = $this->createQueryBuilder('e');
        $qb->andWhere('e.fullName LIKE :q');
        $qb->orderBy('e.sortableName');
        $qb->setParameter('q', "%{$q}%");

        return $qb->getQuery()->execute();
    }

    /**
     * @param type $q
     *
     * @return type
     *
     * @todo This should search the person and alias tables for the name, but
     * only return results from the person table.
     */
    public function searchQuery($q) {
        $qb = $this->createQueryBuilder('e');
        $qb->addSelect('MATCH (e.fullName) AGAINST (:q BOOLEAN) as HIDDEN score');
        $qb->add('where', 'MATCH (e.fullName) AGAINST (:q BOOLEAN) > 0.0');
        $qb->orderBy('score', 'desc');
        $qb->setParameter('q', $q);

        return $qb->getQuery();
    }

    /**
     * @return Query
     */
    public function byRoleQuery(Role $role) {
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.contributions', 'c', Join::WITH, 'c.role = :role');
        $qb->orderBy('p.sortableName');
        $qb->setParameter('role', $role);

        return $qb->getQuery();
    }

    public function byPublisher(Publisher $publisher) {
        $qb = $this->createQueryBuilder('pr');
        $qb->join('pr.contributions', 'c');
        $qb->join('c.publication', 'pb');
        $qb->join('c.role', 'r');
        $qb->andWhere(':publisher MEMBER OF pb.publishers');
        $qb->andWhere('r.name = \'author\'');
        $qb->setParameter('publisher', $publisher);
        $qb->orderBy('pr.sortableName');

        return $qb->getQuery()->execute();
    }
}
