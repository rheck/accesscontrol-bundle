<?php

namespace Rheck\AccessControlBundle\Strategy;

interface PermissionAccessStrategyInterface
{
    public function run($permissions, $context, $criteria);
}