<?php

namespace Rheck\AccessControlBundle\Annotation;

/**
 * @Annotation
 */
class PermissionAccess
{
    public $list;
    public $context = 'SYSTEM';
    public $criteria = 'AND';

    public function __construct($options)
    {
        if (isset($options['value'])) {
            $options['list'] = is_array($options['value']) ? $options['value'] : array($options['value']);
            unset($options['value']);
        } elseif (isset($options['list'])) {
            $options['list'] = is_array($options['list']) ? $options['list'] : array($options['list']);
        }

        foreach ($options as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \InvalidArgumentException(sprintf('Property "%s" does not exist', $key));
            }

            $this->$key = $value;
        }
    }

    public function getList()
    {
        return $this->list;
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getCriteria()
    {
        return $this->criteria;
    }
}
