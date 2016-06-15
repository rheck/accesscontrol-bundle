<?php

namespace Rheck\AccessControlBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Rheck\AccessControlBundle\Entity\PermissionContext
 *
 * @ORM\Table(name="rheck_permissioncontexts")
 * @ORM\Entity(repositoryClass="Rheck\AccessControlBundle\Repository\PermissionContextRepository")
 */
class PermissionContext extends PermissionEntityAbstract
{

    const DEFAULT_CONTEXT = 'SYSTEM';

    /**
     * @var array Permission $permissions
     *
     * @ORM\OneToMany(targetEntity="Permission", mappedBy="permissionContext")
     **/
    protected $permissions;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();

        parent::__construct();
    }

    public function addPermission(Permission $permission)
    {
        $this->permissions[] = $permission;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

}
