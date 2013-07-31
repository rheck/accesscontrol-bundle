<?php

namespace Rheck\AccessControlBundle\Strategy\Criteria;

class AndCriteriaStrategy implements CriteriaStrategyInterface
{
    public function validate($permissions, $countPermissions)
    {
        return sizeof($permissions) == 0;
    }
}
