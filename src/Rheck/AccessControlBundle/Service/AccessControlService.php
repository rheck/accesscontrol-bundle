<?php

namespace Rheck\AccessControlBundle\Service;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Rheck\AccessControlBundle\Exception\InvalidReturnStrategyException;
use Rheck\AccessControlBundle\Exception\MissingInterfaceException;
use Rheck\AccessControlBundle\Exception\StrategyNotFoundException;
use Symfony\Component\Security\Core\SecurityContext;
use Doctrine\ORM\EntityManager;

class AccessControlService
{
    const STRATEGY_INTERFACE = 'Rheck\AccessControlBundle\Strategy\PermissionAccessStrategyInterface';

    protected $serviceContainer;

    public function __construct($serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }

    public function getServiceContainer()
    {
        return $this->serviceContainer;
    }

    public function checkPermission($permissions, $context, $criteria, $strategy)
    {
        try {
            $strategy = $this->getServiceContainer()->get($strategy);
        } catch (ServiceNotFoundException $e) {
            throw new StrategyNotFoundException($strategy);
        }

        $classImplementations = class_implements($strategy);
        if (!in_array(self::STRATEGY_INTERFACE, $classImplementations)) {
            throw new MissingInterfaceException(
                sprintf('Your strategy must implement the interface: %s', self::STRATEGY_INTERFACE)
            );
        }

        $hasPermission = $strategy->run(
            $permissions,
            $context,
            $criteria
        );

        if (!is_bool($hasPermission)) {
            throw new InvalidReturnStrategyException(
                sprintf(
                    'Your "run" method of Strategy must return a boolean value, %s given.',
                    gettype($hasPermission)
                )
            );
        }

        return $hasPermission;
    }
}