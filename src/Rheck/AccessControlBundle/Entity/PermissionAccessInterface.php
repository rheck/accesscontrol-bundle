<?php

namespace Rheck\AccessControlBundle\Entity;

interface PermissionAccessInterface
{
    public function addPermission(Permission $permission);

    public function getPermissions();
}
