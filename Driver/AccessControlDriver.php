<?php

namespace Rheck\AccessControlBundle\Driver;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Rheck\AccessControlBundle\Service\AccessControlService;
use Symfony\Component\HttpKernel\KernelEvents;
use Doctrine\Common\Annotations\Reader;

class AccessControlDriver implements EventSubscriberInterface
{
    private $reader;
    private $accessControlService;

    public function __construct(Reader $reader, AccessControlService $accessControlService)
    {
        $this->reader = $reader;
        $this->accessControlService = $accessControlService;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if (!is_array($controller = $event->getController())) {
            return;
        }

        $object = new \ReflectionObject($controller[0]);
        $method = $object->getMethod($controller[1]);

        $classAnnotation = $this->reader->getClassAnnotation($object, 'Rheck\AccessControlBundle\Annotation\PermissionAccess');
        if (!is_null($classAnnotation)) {
            $this->checkPermission($classAnnotation);
        }

        $methodAnnotation = $this->reader->getMethodAnnotation($method, 'Rheck\AccessControlBundle\Annotation\PermissionAccess');
        if (!is_null($methodAnnotation)) {
            $this->checkPermission($methodAnnotation);
        }
    }

    public function checkPermission($methodAnnotation)
    {
        $hasPermission = $this->accessControlService->checkPermission(
            $methodAnnotation->getPermissions(),
            $methodAnnotation->getContext(),
            $methodAnnotation->getCriteria(),
            $methodAnnotation->getStrategy()
        );

        if (!$hasPermission) {
            throw new AccessDeniedHttpException('You are not allowed to access this route.');
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array(array('onKernelController', 20))
        );
    }
}
