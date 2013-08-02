<?php

namespace Rheck\AccessControlBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;

class PermissionService
{
    protected $objectManager;
    protected $contextService;

    public function __construct(ObjectManager $objectManager, PermissionContextService $contextService)
    {
        $this->objectManager  = $objectManager;
        $this->contextService = $contextService;
    }

    public function getPersistedPermission($permissionName, $contextName)
    {
        $permissionRepository = $this->objectManager->getRepository('RheckAccessControlBundle:Permission');

        $permission = $permissionRepository->findOneByNameAndContext($permissionName, $contextName);
        if (is_null($permission)) {
            $permissionContext = $this->contextService->getPermissionContext($contextName);

            $permission = $permissionRepository->createPermission($permissionName, $permissionContext);
        }

        return $permission;
    }
}
