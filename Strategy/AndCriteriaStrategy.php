<?php

namespace Rheck\AccessControlBundle\Strategy;

class AndCriteriaStrategy implements CriteriaStrategyInterface
{
    public function validate($permissions, $countPermissions)
    {
        return sizeof($permissions) == 0;
    }
}
