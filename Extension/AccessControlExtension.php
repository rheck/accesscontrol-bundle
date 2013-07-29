<?php

namespace Rheck\AccessControlBundle\Extension;

use Rheck\AccessControlBundle\Service\AccessControlService;

class AccessControlExtension extends \Twig_Extension
{
    protected $accessControlService;

    public function __construct(AccessControlService $accessControlService)
    {
        $this->accessControlService = $accessControlService;
    }

    public function getFilters()
    {
        return array();
    }

    public function getFunctions()
    {
        return array(
            'permissionAccess' => new \Twig_Function_Method($this, 'permissionAccess'),
        );
    }

    public function getName()
    {
        return 'accessControlExtension';
    }

    public function permissionAccess(
        $permissions = array(),
        $context     = null,
        $criteria    = "AND",
        $strategy    = "rheck.access_control.default.strategy"
    ) {
        return $this->accessControl->checkPermission($permissions, $context, $criteria, $strategy);
    }
}