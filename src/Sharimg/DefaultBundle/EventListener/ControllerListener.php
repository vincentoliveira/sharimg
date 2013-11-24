<?php

namespace Sharimg\DefaultBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Controller Event Listener
 */
class ControllerListener
{
    /**
     * Call preExecute method on controller action
     * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent $event
     */
    public function onCoreController(FilterControllerEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $_controller = $event->getController();
            if (isset($_controller[0])) {
                $controller = $_controller[0];
                if (method_exists($controller, 'preExecute')) {
                    $controller->preExecute();
                }
            }
        }
    }

}