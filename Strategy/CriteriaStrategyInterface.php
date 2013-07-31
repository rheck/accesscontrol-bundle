<?php

namespace Rheck\AccessControlBundle\Strategy;

interface CriteriaStrategyInterface
{
    public function validate($permissions, $countPermissions);
}
