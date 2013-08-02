<?php

namespace Rheck\AccessControlBundle\Tests\Service;

use Rheck\AccessControlBundle\Service\PermissionContextService;

class PermissionContextServiceTest extends \PHPUnit_Framework_TestCase
{
    private function getRepositoryMock()
    {
        $permissionContextMock = $this->getMock('Rheck\AccessControlBundle\Entity\PermissionContext');

        $repositoryMock = $this->getMockBuilder('Rheck\AccessControlBundle\Repository\PermissionContextRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->any())
            ->method('findOneByName')
            ->will($this->returnValue(null));

        $repositoryMock->expects($this->any())
            ->method('createPermissionContext')
            ->will($this->returnValue($permissionContextMock));

        return $repositoryMock;
    }

    private function getObjectManagerMock($repositoryMock)
    {
        $objectManagerMock = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $objectManagerMock->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repositoryMock));

        return $objectManagerMock;
    }

    public function testGetPermissionContext()
    {
        $contextName       = 'TEST';
        $repositoryMock    = $this->getRepositoryMock();
        $objectManagerMock = $this->getObjectManagerMock($repositoryMock);

        $permissionContextService = new PermissionContextService($objectManagerMock);

        $permissionContext = $permissionContextService->getPermissionContext($contextName);

        $this->assertInstanceOf('Rheck\AccessControlBundle\Entity\PermissionContext', $permissionContext);
    }
}
