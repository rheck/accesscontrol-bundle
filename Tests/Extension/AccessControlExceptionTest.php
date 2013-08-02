<?php

namespace Rheck\AccessControlBundle\Tests\Service;

use Rheck\AccessControlBundle\Extension\AccessControlExtension;

class AccessControlExceptionTest extends \PHPUnit_Framework_TestCase
{
    private function getAccessControlServiceMock()
    {
        $accessControlServiceMock = $this->getMockBuilder('Rheck\AccessControlBundle\Service\AccessControlService')
            ->disableOriginalConstructor()
            ->getMock();

        $accessControlServiceMock->expects($this->any())
            ->method('checkPermission')
            ->will($this->returnValue(true));

        return $accessControlServiceMock;
    }

    public function testGetFilters()
    {
        $accessControlServiceMock = $this->getAccessControlServiceMock();

        $accessControlExtension = new AccessControlExtension($accessControlServiceMock);

        $filters = $accessControlExtension->getFilters();

        $this->assertCount(0, $filters);
    }

    public function testGetFunctions()
    {
        $accessControlServiceMock = $this->getAccessControlServiceMock();

        $accessControlExtension = new AccessControlExtension($accessControlServiceMock);

        $functions = $accessControlExtension->getFunctions();

        $this->assertCount(1, $functions);
    }

    public function testGetName()
    {
        $accessControlServiceMock = $this->getAccessControlServiceMock();

        $accessControlExtension = new AccessControlExtension($accessControlServiceMock);

        $extensionName = $accessControlExtension->getName();

        $this->assertEquals('accessControlExtension', $extensionName);
    }

    public function testPermissionAccess()
    {
        $accessControlServiceMock = $this->getAccessControlServiceMock();

        $accessControlExtension = new AccessControlExtension($accessControlServiceMock);

        $permissionAccess = $accessControlExtension->permissionAccess();

        $this->assertTrue($permissionAccess);
    }
}
