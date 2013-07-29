<?php

namespace Rheck\AccessControlBundle\Tests\DependencyInjection;

use Rheck\AccessControlBundle\DependencyInjection\RheckAccessControlExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RheckAccessControlExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $extension;

    public function testConfigLoad()
    {
        $config = array();
        $this->extension->load(array($config), $container = $this->getContainer());

        $this->assertEquals('user', $container->getParameter('rheck_access_control.has_permissions'));
    }

    protected function setUp()
    {
        $this->extension = new RheckAccessControlExtension();
    }

    private function getContainer()
    {
        $container = new ContainerBuilder();

        return $container;
    }
}