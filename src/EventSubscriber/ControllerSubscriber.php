<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ControllerSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'eventCurrentController',
        ];

    }

    public function eventCurrentController(ControllerEvent $event): void
    {

        // dd(
        //     $event,
            // $event->getKernel(),
            // $event->getRequest(),
            // $event->getRequestType(),
            // $event->isMainRequest(),

            // ACCÉDER AUX DIFFÉRENTES INFORMATIONS DE LA REQUÊTE
            // $event->getRequest()->attributes,
            // $event->getRequest()->server,
            // $event->getRequest()->headers,

        // );

    }

}
