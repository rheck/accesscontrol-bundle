<?php

namespace Rheck\AccessControlBundle\Factory;

class CriteriaFactory
{
    public static function get($strategyName)
    {
        $criteriaStrategies = array(
            'AND' => '\Rheck\AccessControlBundle\Strategy\Criteria\AndCriteriaStrategy',
            'OR'  => '\Rheck\AccessControlBundle\Strategy\Criteria\OrCriteriaStrategy'
        );

        if (!isset($criteriaStrategies[$strategyName])) {
            return false;
        }

        return new $criteriaStrategies[$strategyName]();
    }

}
