<?php

namespace Rheck\AccessControlBundle\Strategy\Criteria;

interface CriteriaStrategyInterface
{
    public function validate($permissions, $countPermissions);
}
