<?php

namespace Rheck\AccessControlBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Rheck\AccessControlBundle\Entity\Permission;

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

    public function createPermission($permissionName, $permissionContext)
    {
        $entityManager = $this->getEntityManager();

        $permission = new Permission();
        $permission->setName($permissionName);
        $permission->setLabel($permissionName);
        $permission->setPermissionContext($permissionContext);

        $entityManager->persist($permission);
        $entityManager->flush();

        return $permission;
    }

}
