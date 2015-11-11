<?php
namespace AppBundle\Resources;

use AppBundle\Controller\ControllerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class Listener
{
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller)) {
            // not a object but a different kind of callable. Do nothing
            return;
        }

        $controllerObject = $controller[0];

        // skip initializing for exceptions
        if ($controllerObject instanceof ExceptionController) {
            return;
        }

        if ($controllerObject instanceof ControllerInterface) {
            // this method is the one that is part of the interface.
            $controllerObject->initialize($event->getRequest());
        }
    }

}