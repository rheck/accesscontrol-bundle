<?php

namespace Rheck\AccessControlBundle\Strategy;

class OrCriteriaStrategy implements CriteriaStrategyInterface
{
    public function validate($permissions, $countPermissions)
    {
        return sizeof($permissions) < $countPermissions;
    }
}
