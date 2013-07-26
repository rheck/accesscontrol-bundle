<?php

namespace Rheck\AccessControlBundle\Strategy;

use Rheck\AccessControlBundle\Entity\Permission;
use Rheck\AccessControlBundle\Entity\PermissionContext;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\PersistentCollection;

class DefaultPermissionAccessStrategy implements PermissionAccessStrategyInterface
{
    const HAS_PERMISSION   = 1;
    const HASNT_PERMISSION = 2;

    protected $objectManager;
    protected $hasPermissions;

    public function __construct(ObjectManager $objectManager, SecurityContextInterface $securityContext, $hasPermissions)
    {
        $this->objectManager = $objectManager;
        $this->securityContext = $securityContext;
        $this->hasPermissions = $hasPermissions;
    }

    public function run($permissions, $context, $criteria)
    {
        $context = mb_strtoupper($context);

        $authToken = $this->securityContext->getToken();
        if (is_null($authToken) || $authToken->isAuthenticated() === false) {
            return false;
        }

        $loggedUser = $authToken->getUser();
        if (is_null($loggedUser)) {
            return false;
        }

        if (!is_array($permissions)) {
            $permissions = array($permissions);
        }

        $permissionRepository = $this->objectManager->getRepository('RheckAccessControlBundle:Permission');
        $countPermissions     = count($permissions);

        $hasPermissionsArray = explode('.', $this->hasPermissions);

        array_shift($hasPermissionsArray);

        $allowedPermissions = $this->getPermissions($loggedUser, $hasPermissionsArray);

        foreach ($permissions as $key => $permission) {
            $permission = mb_strtoupper($permission);

            $persistedPermission = $permissionRepository->findOneByNameAndContext($permission, $context);
            if (is_null($persistedPermission)) {
                $persistedPermission = $this->createPermission($permission, $context);
            }

            if (in_array($persistedPermission, $allowedPermissions->toArray())) {
                unset($permissions[$key]);
            }
        }

        if ((($criteria == "AND") && (sizeof($permissions) == 0)) ||
            (($criteria == "OR") && (sizeof($permissions) < $countPermissions))) {
            return true;
        } else {
            return false;
        }
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

        $this->getPermissions($propertyValue, $objectArray);
    }

    public function createPermission($permissionName, $contextName)
    {
        $permissionContextRepository = $this->objectManager->getRepository('RheckAccessControlBundle:PermissionContext');

        $permissionContext = $permissionContextRepository->findOneByName($contextName);
        if (is_null($permissionContext)) {
            $permissionContext = $this->createPermissionContext($contextName);
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