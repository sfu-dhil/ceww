<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Func;

/**
 * AuthorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AuthorRepository extends EntityRepository
{
    
    /**
     * @param string $q
     * @return Query
     */
    public function searchQuery($q) {
        $qb = $this->createQueryBuilder('a');
        $qb->where(
            $qb->expr()->like(
                new Func('CONCAT', array(
                    'a.fullName, \'\''
                )),
                "'%q%'"
            )
        );
        $qb->orderBy('a.sortableName');
        return $qb->getQuery();
    }
    
}
