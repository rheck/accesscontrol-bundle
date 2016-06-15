<?php

namespace Rheck\AccessControlBundle\Service;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Rheck\AccessControlBundle\Exception\InvalidReturnStrategyException;
use Rheck\AccessControlBundle\Exception\MissingInterfaceException;
use Rheck\AccessControlBundle\Exception\StrategyNotFoundException;
use Rheck\AccessControlBundle\Adapter\ServiceContainerAdapter;

class AccessControlService
{
    const STRATEGY_INTERFACE = 'Rheck\AccessControlBundle\Strategy\PermissionAccess\PermissionAccessStrategyInterface';

    const CRITERIA_AND = "AND";
    const CRITERIA_OR  = "OR";

    protected $serviceContainerAdapter;

    public function __construct(ServiceContainerAdapter $serviceContainerAdapter)
    {
        $this->serviceContainerAdapter = $serviceContainerAdapter;
    }

    public function getServiceContainerAdapter()
    {
        return $this->serviceContainerAdapter;
    }

    public function getStrategy($strategy)
    {
        $strategyContainer = $this->getServiceContainerAdapter();

        try {
            return $strategyContainer->get($strategy);
        } catch (ServiceNotFoundException $e) {
            throw new StrategyNotFoundException($strategy);
        }
    }

    public function checkStrategyImplementation($strategy)
    {
        $classImplementations = class_implements($strategy);

        if (!in_array(self::STRATEGY_INTERFACE, $classImplementations)) {
            throw new MissingInterfaceException(
                sprintf('Your strategy must implement the interface: %s', self::STRATEGY_INTERFACE)
            );
        }

        return true;
    }

    public function checkStrategyReturn($strategyReturn)
    {
        if (is_bool($strategyReturn)) {
            return $strategyReturn;
        }

        throw new InvalidReturnStrategyException(
            sprintf(
                'Your "run" method of Strategy must return a boolean value, %s given.',
                gettype($strategyReturn)
            )
        );
    }

    public function checkPermission($permissions, $context, $criteria, $strategy)
    {
        $strategy = $this->getStrategy($strategy);

        $this->checkStrategyImplementation($strategy);

        $hasPermission = $strategy->run(
            $permissions,
            $context,
            $criteria
        );

        return $this->checkStrategyReturn($hasPermission);
    }
}
