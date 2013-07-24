<?php

namespace Rheck\AccessControlBundle\Service;

use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityManager;

class AccessControlService
{
    const HAS_PERMISSION   = 1;
    const HASNT_PERMISSION = 2;

    protected $securityContext;
    protected $entityManager;

    public function __construct(EntityManager $entityManager, SecurityContext $securityContext)
    {
        $this->entityManager   = $entityManager;
        $this->securityContext = $securityContext;
    }

    public function hasPermissions($permissionsToCheck, $context = PermissionContext::DEFAULT_CONTEXT, $criteria = "AND")
    {
        if (is_null($context)) {
            $context = PermissionContext::DEFAULT_CONTEXT;
        }

        $context   = strtoupper($context);
        $authToken = $this->securityContext->getToken();
        if (is_null($authToken)) {
            return false;
        }

        $loggedUser = $authToken->getUser();
        if (is_null($loggedUser)) {
            return false;
        }

        if (!is_array($permissionsToCheck)) {
            $singlePerm         = $permissionsToCheck;
            $permissionsToCheck = array($singlePerm);
        }

        $permissionRepository = $this->entityManager->getRepository('RheckAccessControlBundle:Permission');
        $numPermissions       = sizeof($permissionsToCheck);

        foreach ($loggedUser->getUserGroups() as $userGroup) {
            $rolePermissions = $userGroup->getPermissions();
            foreach ($permissionsToCheck as $key => $permissionName) {
                $permissionName   = strtoupper($permissionName);
                $permissionToFind = $permissionRepository->findOneByNameAndContext($permissionName, $context);
                if (is_null($permissionToFind)) {
                    $permissionToFind = $this->createPermission($permissionName, $context);
                }

                if (in_array($permissionToFind, $rolePermissions->toArray())) {
                    unset($permissionsToCheck[$key]);
                }
            }

            $permissionsToCheck = array_values($permissionsToCheck);
        }

        if ((($criteria == "AND") && (sizeof($permissionsToCheck) == 0)) ||
            (($criteria == "OR") && (sizeof($permissionsToCheck) < $numPermissions))) {
            return true;
        } else {
            return false;
        }
    }

    public function createPermission($permissionName, $contextName)
    {
        $permissionContextRepository = $this->entityManager->getRepository('RheckAccessControlBundle:PermissionContext');

        $permissionContext = $permissionContextRepository->findOneByName($contextName);
        if (is_null($permissionContext)) {
            $permissionContext = $this->createContext($contextName);
        }

        $permission = new Permission();
        $permission->setName($permissionName);
        $permission->setLabel($permissionName);
        $permission->setPermissionContext($permissionContext);

        $this->em->persist($permission);
        $this->em->flush();

        return $permission;
    }

    public function createPermissionContext($contextName)
    {
        $permissionContext = new PermissionContext();
        $permissionContext->setName($contextName);
        $permissionContext->setLabel($contextName);

        return $permissionContext;
    }
}