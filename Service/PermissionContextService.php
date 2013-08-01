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
        $contextRepository = $this->objectManager->getRepository('RheckAccessControlBundle:PermissionContext');

        $permissionContext = $contextRepository->findOneByName($contextName);
        if (is_null($permissionContext)) {
            $permissionContext = $contextRepository->createPermissionContext($contextName);
        }

        return $permissionContext;
    }
}