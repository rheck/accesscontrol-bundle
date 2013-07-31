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
        $allowedPermissions   = $this->getAllowedPermissions($loggedUser);

        foreach ($permissions as $key => $permission) {
            $permission = mb_strtoupper($permission);

            $persistedPermission = $this->permissionService->getPersistedPermission($permission, $context);

            if (in_array($persistedPermission, $allowedPermissions)) {
                unset($permissions[$key]);
            }
        }

        $andCriteria = $this->checkAndCriteria($permissions, $criteria);
        $orCriteria  = $this->checkOrCriteria($permissions, $countPermissions, $criteria);

        return $andCriteria || $orCriteria;
    }

    public function checkAndCriteria($permissions, $criteria)
    {
        return $criteria == AccessControlService::CRITERIA_AND && sizeof($permissions) == 0;
    }

    public function checkOrCriteria($permissions, $countPermissions, $criteria)
    {
        return $criteria == AccessControlService::CRITERIA_OR && sizeof($permissions) < $countPermissions;
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
