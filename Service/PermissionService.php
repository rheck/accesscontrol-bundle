<?php

namespace Rheck\AccessControlBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;

class PermissionService
{
    protected $objectManager;
    protected $permissionContextService;

    public function __construct(ObjectManager $objectManager, PermissionContextService $permissionContextService)
    {
        $this->objectManager            = $objectManager;
        $this->permissionContextService = $permissionContextService;
    }

    public function getPersistedPermission($permissionName, $contextName)
    {
        $permissionRepository = $this->objectManager->getRepository('RheckAccessControlBundle:Permission');

        $permission = $permissionRepository->findOneByNameAndContext($permissionName, $contextName);
        if (is_null($permission)) {
            $permissionContext = $this->permissionContextService->getPermissionContext($contextName);

            $permission = $permissionRepository->createPermission($permissionName, $permissionContext);
        }

        return $permission;
    }
}