<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;

abstract class AbstractDataGridRepository extends ServiceEntityRepository
{
    /**
     * There is an issue in symfony. It doesn't allow to get total number of items, if search criteria is not empty.
     * That's why I have to implement this overflow.
     *
     * @param Criteria $criteria
     * @return int
     */
    public function total(Criteria $criteria): int
    {
        return $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName)->count($criteria);
    }
}