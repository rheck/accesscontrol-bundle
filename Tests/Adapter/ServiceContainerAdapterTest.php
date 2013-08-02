<?php

namespace Rheck\AccessControlBundle\Tests\Adapter;

use Rheck\AccessControlBundle\Adapter\ServiceContainerAdapter;

class ServiceContainerAdapterTest extends \PHPUnit_Framework_TestCase
{
    private function getServiceContainerMock()
    {
        $permissionStrategyMock = $this->getMock('Rheck\AccessControlBundle\Strategy\PermissionAccess\PermissionAccessStrategyInterface');

        $serviceContainerMock = $this->getMock('Rheck\AccessControlBundle\Adapter\ServiceContainerInterface');

        $serviceContainerMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($permissionStrategyMock));

        return $serviceContainerMock;
    }

    public function testGetServiceContainer()
    {
        $serviceContainerMock = $this->getServiceContainerMock();

        $serviceContainerAdapter = new ServiceContainerAdapter();
        $serviceContainerAdapter->setServiceContainer($serviceContainerMock);

        $serviceContainer = $serviceContainerAdapter->getServiceContainer();

        $this->assertInstanceOf('Rheck\AccessControlBundle\Adapter\ServiceContainerInterface', $serviceContainer);
    }

    public function testGet()
    {
        $serviceContainerMock = $this->getServiceContainerMock();

        $serviceContainerAdapter = new ServiceContainerAdapter();
        $serviceContainerAdapter->setServiceContainer($serviceContainerMock);

        $permissionStrategy = $serviceContainerAdapter->get('testing');

        $this->assertInstanceOf('Rheck\AccessControlBundle\Strategy\PermissionAccess\PermissionAccessStrategyInterface', $permissionStrategy);
    }
}
