<?php

namespace Rheck\AccessControlBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rheck\AccessControlBundle\Entity\Permission
 *
 * @ORM\Table(name="rheck_permissions")
 * @ORM\Entity(repositoryClass="Rheck\AccessControlBundle\Repository\PermissionRepository")
 */
class Permission extends PermissionEntityAbstract
{

    /**
     * @var PermissionContext $permissionContext
     *
     * @ORM\ManyToOne(targetEntity="PermissionContext", inversedBy="permissions", cascade={"persist"})
     */
    protected $permissionContext;

    public function setPermissionContext(PermissionContext $permissionContext)
    {
        $this->permissionContext = $permissionContext;
    }

    public function getPermissionContext()
    {
        return $this->permissionContext;
    }

}
