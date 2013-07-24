<?php

namespace Rheck\AccessControlBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PermissionRepository extends EntityRepository
{
    public function findOneByNameAndContext($permissionName, $contextName)
    {
        $qb = $this->createQueryBuilder('pe')
            ->select('p')
            ->from('ConradCaineCoreLibraryEntityBundle:Permission', 'p')
            ->join('p.permissionContext', 'c')
            ->where('p.name = :permissionName')
            ->andWhere('c.name = :contextName');

        $qb->setParameter('permissionName', $permissionName);
        $qb->setParameter('contextName', $contextName);

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }

}
