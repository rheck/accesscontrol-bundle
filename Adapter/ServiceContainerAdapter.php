<?php

namespace Rheck\AccessControlBundle\Adapter;

class ServiceContainerAdapter
{
    protected $serviceContainer;

    public function setServiceContainer($serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }

    public function getServiceContainer()
    {
        return $this->serviceContainer;
    }

    public function get($serviceName)
    {
        return $this->serviceContainer->get($serviceName);
    }
}