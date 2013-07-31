<?php

namespace Rheck\AccessControlBundle\Strategy\PermissionAccess;

interface PermissionAccessStrategyInterface
{
    public function run($permissions, $context, $criteria);
}