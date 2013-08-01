<?php

namespace Rheck\AccessControlBundle\Strategy\PermissionAccess;

use Rheck\AccessControlBundle\Adapter\SecurityContextAdapter;
use Rheck\AccessControlBundle\Factory\CriteriaFactory;
use Rheck\AccessControlBundle\Service\AccessControlService;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Rheck\AccessControlBundle\Service\PermissionService;
use Doctrine\ORM\PersistentCollection;

class DefaultPermissionAccessStrategy implements PermissionAccessStrategyInterface
{
    protected $securityContextAdapter;
    protected $permissionService;
    protected $hasPermissions;

    public function setSecurityContextAdapter(SecurityContextAdapter $securityContextAdapter)
    {
        $this->securityContextAdapter = $securityContextAdapter;
    }

    public function setPermissionService(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function setHasPermissions($hasPermissions)
    {
        $this->hasPermissions = $hasPermissions;
    }

    public function run($permissions, $context, $criteria)
    {
        if (false === ($loggedUser = $this->securityContextAdapter->getLogged())) {
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

        return $permissions;
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
            return $this->getEntityPermissions($object);
        }

        $reflectionObject   = new \ReflectionObject($object);
        $reflectionProperty = $reflectionObject->getProperty(array_shift($objectArray));

        $reflectionProperty->setAccessible(true);
        $propertyValue = $reflectionProperty->getValue($object);

        return $this->getPermissions($propertyValue, $objectArray);
    }

}
