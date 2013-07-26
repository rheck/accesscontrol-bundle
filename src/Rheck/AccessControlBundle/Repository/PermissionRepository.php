<?php

namespace Rheck\AccessControlBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PermissionRepository extends EntityRepository
{
    public function findOneByNameAndContext($permissionName, $contextName)
    {
        $qb = $this->createQueryBuilder('pe')
            ->select('p')
            ->from('RheckAccessControlBundle:Permission', 'p')
            ->join('p.permissionContext', 'pc')
            ->where('p.name = :permissionName')
            ->andWhere('pc.name = :contextName');

        $qb->setParameter('permissionName', $permissionName);
        $qb->setParameter('contextName', $contextName);

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }

}
