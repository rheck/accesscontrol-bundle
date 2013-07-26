<?php

namespace Rheck\AccessControlBundle\Driver;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Rheck\AccessControlBundle\Service\AccessControlService;
use Rheck\AccessControlBundle\Annotation\PermissionAccess;
use Doctrine\Common\Annotations\Reader;

class AccessControlDriver
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

        foreach ($this->reader->getMethodAnnotations($method) as $methodAnnotation) {
            if ($methodAnnotation instanceof PermissionAccess) {
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
        }
    }
}