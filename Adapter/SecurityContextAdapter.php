<?php

namespace Rheck\AccessControlBundle\Adapter;

use Symfony\Component\Security\Core\SecurityContextInterface;

class SecurityContextAdapter
{
    protected $securityContext;

    public function setSecurityContext(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function getSecurityContext()
    {
        return $this->securityContext;
    }

    public function getLogged()
    {
        $authToken = $this->getSecurityContext()->getToken();

        if (!$authToken || !$authToken->isAuthenticated()) {
            return false;
        }

        $loggedUser = $authToken->getUser();

        return !is_null($loggedUser) ? $loggedUser : false;
    }
}
