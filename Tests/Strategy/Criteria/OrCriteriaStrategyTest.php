<?php

namespace Rheck\AccessControlBundle\Tests\Service;

use Rheck\AccessControlBundle\Strategy\Criteria\OrCriteriaStrategy;

class OrCriteriaStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testValidate()
    {
        $orCriteriaStrategy = new OrCriteriaStrategy();

        $validation = $orCriteriaStrategy->validate(array(), 1);

        $this->assertTrue($validation);
    }

    public function testValidateFalse()
    {
        $orCriteriaStrategy = new OrCriteriaStrategy();

        $validation = $orCriteriaStrategy->validate(array('testing'), 1);

        $this->assertFalse($validation);
    }
}
