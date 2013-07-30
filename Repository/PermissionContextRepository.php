<?php

namespace Rheck\AccessControlBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Rheck\AccessControlBundle\Entity\PermissionContext;

class PermissionContextRepository extends EntityRepository
{

    public function createPermissionContext($contextName)
    {
        $permissionContext = new PermissionContext();
        $permissionContext->setName($contextName);
        $permissionContext->setLabel($contextName);

        return $permissionContext;
    }

}
