<?php

namespace App\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class ChoiceRepository extends EntityRepository
{
    /**
     * @return mixed
     */
    public function countResult()
    {
        $qb = $this->createQueryBuilder('choice');

        $result = $qb->select("choice.film, COUNT('*') AS votes")
                  ->groupBy('choice.film')
                  ->orderBy('votes', 'DESC')
                  ->getQuery()
                  ->getResult();

        return $result;
    }


}
