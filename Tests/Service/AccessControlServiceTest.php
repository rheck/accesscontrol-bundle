<?php

namespace Rheck\AccessControlBundle\Tests\Service;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Rheck\AccessControlBundle\Service\AccessControlService;

class AccessControlServiceTest extends \PHPUnit_Framework_TestCase
{
    private function getServiceContainer()
    {
        $strategyInterface = $this->getMock('Rheck\AccessControlBundle\Strategy\PermissionAccess\PermissionAccessStrategyInterface');

        $strategyInterface->expects($this->any())
            ->method('run')
            ->will($this->returnValue(true));

        $serviceContainer = $this->getMock('Rheck\AccessControlBundle\Adapter\ServiceContainerAdapter');

        $serviceContainer->expects($this->any())
            ->method('get')
            ->will($this->returnValue($strategyInterface));

        return $serviceContainer;
    }

    private function getAlternativeServiceContainer()
    {
        $serviceContainer = $this->getMock('Rheck\AccessControlBundle\Adapter\ServiceContainerAdapter');

        $serviceContainer->expects($this->any())
            ->method('get')
            ->will($this->throwException(new ServiceNotFoundException('testing')));

        return $serviceContainer;
    }

    public function testCheckPermission()
    {
        $permission = 'TEST';
        $context    = 'SYSTEM';
        $criteria   = 'AND';
        $strategy   = 'DUMMY';

        $serviceContainer = $this->getServiceContainer();

        $accessControlService = new AccessControlService($serviceContainer);

        $isAllowed = $accessControlService->checkPermission($permission, $context, $criteria, $strategy);

        $this->assertTrue($isAllowed);
    }

    /**
     * @expectedException Rheck\AccessControlBundle\Exception\InvalidReturnStrategyException
     */
    public function testCheckPermissionInvalidReturnStrategyException()
    {
        $serviceContainer = $this->getServiceContainer();

        $accessControlService = new AccessControlService($serviceContainer);

        $accessControlService->checkStrategyReturn('throw exception');
    }

    /**
     * @expectedException Rheck\AccessControlBundle\Exception\MissingInterfaceException
     */
    public function testCheckPermissionMissingInterfaceException()
    {
        $serviceContainer = $this->getServiceContainer();

        $accessControlService = new AccessControlService($serviceContainer);

        $accessControlService->checkStrategyImplementation($this);
    }

    /**
     * @expectedException Rheck\AccessControlBundle\Exception\StrategyNotFoundException
     */
    public function testCheckPermissionStrategyNotFoundException()
    {
        $serviceContainer = $this->getAlternativeServiceContainer();

        $accessControlService = new AccessControlService($serviceContainer);

        $accessControlService->getStrategy('throw exception');
    }
}
