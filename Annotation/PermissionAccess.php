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

        if (isset($options['permissions'])) {
            $options['permissions'] = is_array($options['permissions']) ? $options['permissions'] : array($options['permissions']);
        }

        foreach ($options as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist', $key));
            }

            $this->$key = $value;
        }
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getCriteria()
    {
        return $this->criteria;
    }

    public function getStrategy()
    {
        return $this->strategy;
    }
}
