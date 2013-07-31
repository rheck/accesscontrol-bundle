<?php

namespace Rheck\AccessControlBundle\Strategy;

use Rheck\AccessControlBundle\Factory\CriteriaFactory;
use Rheck\AccessControlBundle\Service\AccessControlService;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Rheck\AccessControlBundle\Service\PermissionService;
use Doctrine\ORM\PersistentCollection;

class DefaultPermissionAccessStrategy implements PermissionAccessStrategyInterface
{
    protected $permissionService;
    protected $securityContext;
    protected $hasPermissions;

    public function __construct(
        SecurityContextInterface $securityContext,
        PermissionService $permissionService,
        $hasPermissions
    ) {
        $this->permissionService = $permissionService;
        $this->securityContext   = $securityContext;
        $this->hasPermissions    = $hasPermissions;
    }

    public function getLoggedUser()
    {
        $authToken = $this->securityContext->getToken();
        if (is_null($authToken) || $authToken->isAuthenticated() === false) {
            return false;
        }

        $loggedUser = $authToken->getUser();
        if (is_null($loggedUser)) {
            return false;
        }

        return $loggedUser;
    }

    public function run($permissions, $context, $criteria)
    {
        if (false === ($loggedUser = $this->getLoggedUser())) {
            return false;
        }

        $context              = mb_strtoupper($context);
        $criteria             = mb_strtoupper($criteria);

        $permissions          = is_array($permissions) ? $permissions : array($permissions);
        $countPermissions     = count($permissions);

        $allowedPermissions   = $this->getAllowedPermissions($loggedUser);
        $unallowedPermissions = $this->getUnallowedPermissions($permissions, $allowedPermissions, $context);

        $criteriaStrategy = CriteriaFactory::get($criteria);

        return $criteriaStrategy->validate($unallowedPermissions, $countPermissions);
    }

    public function getUnallowedPermissions($permissions, $allowedPermissions, $context)
    {
        foreach ($permissions as $key => $permission) {
            $permission = mb_strtoupper($permission);

            $persistedPermission = $this->permissionService->getPersistedPermission($permission, $context);

            if (in_array($persistedPermission, $allowedPermissions)) {
                unset($permissions[$key]);
            }
        }
    }

    public function getAllowedPermissions($loggedUser)
    {
        $hasPermissionsArray = explode('.', $this->hasPermissions);

        array_shift($hasPermissionsArray);

        return $this->getPermissions($loggedUser, $hasPermissionsArray);
    }

    public function getEntityPermissions($object)
    {
        if ($object instanceof PersistentCollection) {
            $permissions = array();

            foreach ($object as $o) {
                $permissions = array_merge($permissions, $o->getPermissions()->toArray());
            }

            return $permissions;
        }

        return $object->getPermissions();
    }

    public function getPermissions($object, $objectArray)
    {
        if (!count($objectArray)) {
            $this->getEntityPermissions($object);
        }

        $reflectionObject   = new \ReflectionObject($object);
        $reflectionProperty = $reflectionObject->getProperty(array_shift($objectArray));

        $reflectionProperty->setAccessible(true);
        $propertyValue = $reflectionProperty->getValue($object);

        return $this->getPermissions($propertyValue, $objectArray);
    }

}
