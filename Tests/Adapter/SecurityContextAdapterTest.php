<?php

namespace Rheck\AccessControlBundle\Tests\Adapter;

use Rheck\AccessControlBundle\Adapter\SecurityContextAdapter;

class SecurityContextAdapterTest extends \PHPUnit_Framework_TestCase
{
    private function getTokenMock($userMock)
    {
        $tokenMock = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $tokenMock->expects($this->any())
            ->method('isAuthenticated')
            ->will($this->returnValue(true));
        $tokenMock->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($userMock));

        return $tokenMock;
    }

    private function getSecurityContextMock($tokenMock)
    {
        $securityContextMock = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $securityContextMock->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($tokenMock));

        return $securityContextMock;
    }

    public function testGetLogged()
    {
        $userMock = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $tokenMock = $this->getTokenMock($userMock);
        $securityContextMock = $this->getSecurityContextMock($tokenMock);

        $securityContextAdapter = new SecurityContextAdapter();
        $securityContextAdapter->setSecurityContext($securityContextMock);

        $loggedUser = $securityContextAdapter->getLogged();

        $this->assertInstanceOf(
            'Symfony\Component\Security\Core\SecurityContextInterface',
            $securityContextAdapter->getSecurityContext()
        );

        $this->assertInstanceOf(
            'Symfony\Component\Security\Core\User\UserInterface',
            $loggedUser
        );
    }
}
