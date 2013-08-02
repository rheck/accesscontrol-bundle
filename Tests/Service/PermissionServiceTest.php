<?php

namespace Rheck\AccessControlBundle\Tests\Service;

use Rheck\AccessControlBundle\Service\PermissionService;

class PermissionServiceTest extends \PHPUnit_Framework_TestCase
{
    private function getRepositoryMock()
    {
        $permissionMock = $this->getMock('Rheck\AccessControlBundle\Entity\Permission');

        $repositoryMock = $this->getMockBuilder('Rheck\AccessControlBundle\Repository\PermissionRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->any())
            ->method('findOneByNameAndContext')
            ->will($this->returnValue(null));

        $repositoryMock->expects($this->any())
            ->method('createPermission')
            ->will($this->returnValue($permissionMock));

        return $repositoryMock;
    }

    private function getContextService()
    {
        $permissionContextMock = $this->getMock('Rheck\AccessControlBundle\Entity\Permission');

        $contextService = $this->getMockBuilder('Rheck\AccessControlBundle\Service\PermissionContextService')
            ->disableOriginalConstructor()
            ->getMock();

        $contextService->expects($this->any())
            ->method('getPermissionContext')
            ->will($this->returnValue($permissionContextMock));

        return $contextService;
    }

    private function getObjectManagerMock($repositoryMock)
    {
        $objectManagerMock = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $objectManagerMock->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repositoryMock));

        return $objectManagerMock;
    }

    public function testGetPersistedPermission()
    {
        $permissionName    = 'TESTING';
        $contextName       = 'TEST';
        $repositoryMock    = $this->getRepositoryMock();
        $contextService    = $this->getContextService();
        $objectManagerMock = $this->getObjectManagerMock($repositoryMock);

        $permissionService = new PermissionService($objectManagerMock, $contextService);

        $permission = $permissionService->getPersistedPermission($permissionName, $contextName);

        $this->assertInstanceOf('Rheck\AccessControlBundle\Entity\Permission', $permission);
    }
}
