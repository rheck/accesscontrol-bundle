<?php

namespace Rheck\AccessControlBundle\Adapter;

interface ServiceContainerInterface
{
    public function get($serviceName);
}
