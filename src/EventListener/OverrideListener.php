<?php

namespace App\EventListener;
 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
 
class OverrideListener
{
    public function onKernelController(ControllerEvent $event)
    {
        
        $message = 'Page en maintenance - OverrideListener message';
 
        if ($event->getRequest()->get('_route') === 'products') {

            // Override controller response
            $event->setController(

                function() use ($message) {

                    return new Response($message, 400);

                }

            );

        }
        
    }

}