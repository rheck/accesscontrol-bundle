<?php

namespace Rheck\AccessControlBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;

class PermissionContextService
{
    protected $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function getPermissionContext($contextName)
    {
        $permissionContextRepository = $this->objectManager->getRepository('RheckAccessControlBundle:PermissionContext');

        $permissionContext = $permissionContextRepository->findOneByName($contextName);
        if (is_null($permissionContext)) {
            $permissionContext = $permissionContextRepository->createPermissionContext($contextName);
        }

        return $permissionContext;
    }
}