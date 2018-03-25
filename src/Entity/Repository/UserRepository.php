<?php

namespace App\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class UserRepository
 * @package App\Entity\Repository
 */
class UserRepository extends EntityRepository
{
    /**
     * @param $choice
     * @return mixed
     */
    public function findByChoice($choice)
    {
        $querybuilder = $this
            ->createQueryBuilder('user')
            ->innerJoin('user.choices', 'choice')
            ->where("choice.film = :film")
            ->setParameter('film', $choice);

        return $querybuilder->getQuery()->execute();
    }
}
