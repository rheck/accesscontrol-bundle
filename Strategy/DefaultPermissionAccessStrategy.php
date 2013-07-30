<?php

namespace Rheck\AccessControlBundle\Strategy;

use Rheck\AccessControlBundle\Service\AccessControlService;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Rheck\AccessControlBundle\Service\PermissionService;
use Doctrine\ORM\PersistentCollection;

class DefaultPermissionAccessStrategy implements PermissionAccessStrategyInterface
{
    protected $permissionService;
    protected $securityContext;
    protected $hasPermissions;

    public function __construct(SecurityContextInterface $securityContext, PermissionService $permissionService, $hasPermissions)
    {
        $this->securityContext   = $securityContext;
        $this->permissionService = $permissionService;
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

        $permissions          = is_array($permissions) ? $permissions : array($permissions);
        $countPermissions     = count($permissions);
        $context              = mb_strtoupper($context);
        $allowedPermissions   = $this->getUserPermissions($loggedUser);

        foreach ($permissions as $key => $permission) {
            $permission = mb_strtoupper($permission);

            $persistedPermission = $this->permissionService->getPersistedPermission($permission, $context);

            if (in_array($persistedPermission, $allowedPermissions)) {
                unset($permissions[$key]);
            }
        }

        if ((($criteria == AccessControlService::CRITERIA_AND) && (sizeof($permissions) == 0)) ||
            (($criteria == AccessControlService::CRITERIA_OR) && (sizeof($permissions) < $countPermissions))) {
            return true;
        }

        return false;
    }

    public function getUserPermissions($loggedUser)
    {
        $hasPermissionsArray = explode('.', $this->hasPermissions);

        array_shift($hasPermissionsArray);

        return $this->getPermissions($loggedUser, $hasPermissionsArray);
    }

    public function getPermissions($object, $objectArray)
    {
        if (!count($objectArray)) {
            if ($object instanceof PersistentCollection) {
                $permissions = array();

                foreach ($object as $o) {
                    $permissions = array_merge($permissions, $o->getPermissions()->toArray());
                }

                return $permissions;
            }

            return $object->getPermissions();
        }

        $reflectionObject   = new \ReflectionObject($object);
        $reflectionProperty = $reflectionObject->getProperty(array_shift($objectArray));

        $isPrivateOrProtected = false;
        if ($reflectionProperty->isPrivate() || $reflectionProperty->isProtected()) {
            $isPrivateOrProtected = true;
            $reflectionProperty->setAccessible(true);
        }

        $propertyValue = $reflectionProperty->getValue($object);

        if ($isPrivateOrProtected) {
            $reflectionProperty->setAccessible(false);
        }

        return $this->getPermissions($propertyValue, $objectArray);
    }

}
