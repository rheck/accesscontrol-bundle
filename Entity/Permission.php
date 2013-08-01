<?php

namespace Rheck\AccessControlBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rheck\AccessControlBundle\Entity\Permission
 *
 * @ORM\Table(name="rheck_permissions")
 * @ORM\Entity(repositoryClass="Rheck\AccessControlBundle\Repository\PermissionRepository")
 */
class Permission
{

    /**
     * @var integer $identifier
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $identifier;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string $label
     *
     * @ORM\Column(name="label", type="string", length=255)
     */
    protected $label;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    protected $description;

    /**
     * @var PermissionContext $permissionContext
     *
     * @ORM\ManyToOne(targetEntity="PermissionContext", inversedBy="permissions", cascade={"persist"})
     */
    protected $permissionContext;

    public function __construct()
    {
        $this->setDescription('Auto-generated by system');
    }

    public function getId()
    {
        return $this->identifier;
    }

    public function setName($name)
    {
        $this->name = strtoupper($name);
    }

    public function getName()
    {
        return strtoupper($this->name);
    }

    public function setLabel($label)
    {
        if ($this->getName() === $label) {
            $newLabel = mb_strtolower($label, 'UTF-8');
            $newLabel = str_replace('_', ' ', $newLabel);
            $this->label = ucwords($newLabel);
        } else {
            $this->label = $label;
        }
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setPermissionContext(PermissionContext $permissionContext)
    {
        $this->permissionContext = $permissionContext;
    }

    public function getPermissionContext()
    {
        return $this->permissionContext;
    }

}
