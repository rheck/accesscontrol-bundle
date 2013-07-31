<?php

namespace Rheck\AccessControlBundle\Annotation;

use Rheck\AccessControlBundle\Entity\PermissionContext;

/**
 * @Annotation
 */
class PermissionAccess
{
    private $permissions = array();
    private $context     = PermissionContext::DEFAULT_CONTEXT;
    private $criteria    = 'AND';
    private $strategy    = "rheck.access_control.default.strategy";

    public function __construct(array $options)
    {
        if (isset($options['value'])) {
            $options['permissions'] = $options['value'];
            unset($options['value']);
        }

        foreach ($options as $key => $value) {
            $method = 'set'.str_replace('_', '', $key);
            if (!method_exists($this, $method)) {
                throw new \BadMethodCallException(sprintf("Unknown property '%s' on annotation '%s'.", $key, get_class($this)));
            }
            $this->$method($value);
        }
    }

    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

    public function setContext($context)
    {
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;
    }

    public function getCriteria()
    {
        return $this->criteria;
    }

    public function setStrategy($strategy)
    {
        $this->strategy = $strategy;
    }

    public function getStrategy()
    {
        return $this->strategy;
    }
}
