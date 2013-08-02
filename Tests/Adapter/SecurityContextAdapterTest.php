<?php

namespace Rheck\AccessControlBundle\Tests\Adapter;

use Rheck\AccessControlBundle\Adapter\SecurityContextAdapter;

class SecurityContextAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetLogged()
    {
        $userMock = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');

        $tokenMock = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $tokenMock->expects($this->any())
            ->method('isAuthenticated')
            ->will($this->returnValue(true));
        $tokenMock->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($userMock));

        $securityContextMock = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $securityContextMock->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($tokenMock));

        $securityContextAdapter = new SecurityContextAdapter();
        $securityContextAdapter->setSecurityContext($securityContextMock);

        $this->assertInstanceOf(
            'Symfony\Component\Security\Core\SecurityContextInterface',
            $securityContextAdapter->getSecurityContext()
        );

        $loggedUser = $securityContextAdapter->getLogged();

        $this->assertInstanceOf(
            'Symfony\Component\Security\Core\User\UserInterface',
            $loggedUser
        );
    }
}
