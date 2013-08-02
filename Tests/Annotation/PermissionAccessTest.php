<?php

namespace Rheck\AccessControlBundle\Tests\Annotation;

use Rheck\AccessControlBundle\Annotation\PermissionAccess;

class PermissionAccessTest extends \PHPUnit_Framework_TestCase
{
    private function getRightOptions()
    {
        return array(
            'value'    => 'testing',
            'context'  => 'SYSTEM',
            'criteria' => 'AND',
            'strategy' => 'DUMMY'
        );
    }

    private function getWrongOptions()
    {
        return array(
            'throw' => 'exception'
        );
    }

    private function getPermission($options)
    {
        return new PermissionAccess($options);
    }

    public function testGetPermissions()
    {
        $options    = $this->getRightOptions();
        $permission = $this->getPermission($options);

        $permissions = $permission->getPermissions();

        $this->assertEquals('testing', $permissions);
    }

    public function testGetContext()
    {
        $options    = $this->getRightOptions();
        $permission = $this->getPermission($options);

        $context = $permission->getContext();

        $this->assertEquals('SYSTEM', $context);
    }

    public function testGetCriteria()
    {
        $options    = $this->getRightOptions();
        $permission = $this->getPermission($options);

        $criteria = $permission->getCriteria();

        $this->assertEquals('AND', $criteria);
    }

    public function testGetStrategy()
    {
        $options    = $this->getRightOptions();
        $permission = $this->getPermission($options);

        $strategy = $permission->getStrategy();

        $this->assertEquals('DUMMY', $strategy);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testConstructBadMethodCallException()
    {
        $options    = $this->getWrongOptions();
        $this->getPermission($options);
    }
}
