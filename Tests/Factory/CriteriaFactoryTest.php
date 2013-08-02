<?php

namespace Rheck\AccessControlBundle\Tests\Factory;

use Rheck\AccessControlBundle\Factory\CriteriaFactory;

class CriteriaFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $andCriteriaStrategy   = CriteriaFactory::get('AND');
        $orCriteriaStrategy    = CriteriaFactory::get('OR');
        $otherCriteriaStrategy = CriteriaFactory::get('OTHER');

        $this->assertInstanceOf('Rheck\AccessControlBundle\Strategy\Criteria\CriteriaStrategyInterface', $andCriteriaStrategy);
        $this->assertInstanceOf('Rheck\AccessControlBundle\Strategy\Criteria\CriteriaStrategyInterface', $orCriteriaStrategy);
        $this->assertFalse($otherCriteriaStrategy);
    }
}
