<?php

namespace Rheck\AccessControlBundle\Tests\Service;

use Rheck\AccessControlBundle\Strategy\Criteria\AndCriteriaStrategy;

class AndCriteriaStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testValidate()
    {
        $andCriteriaStrategy = new AndCriteriaStrategy();

        $validation = $andCriteriaStrategy->validate(array(), 0);

        $this->assertTrue($validation);
    }

    public function testValidateFalse()
    {
        $andCriteriaStrategy = new AndCriteriaStrategy();

        $validation = $andCriteriaStrategy->validate(array('testing'), 0);

        $this->assertFalse($validation);
    }
}
