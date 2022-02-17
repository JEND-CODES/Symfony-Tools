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

        // $request = $event->getRequest();

        // dd(
        //     $event,
            // $event->getKernel(),
            // $event->getRequest(),
            // $event->getRequestType(),
            // $event->isMainRequest(),

            // ACCÉDER AUX DIFFÉRENTES INFORMATIONS DE LA REQUÊTE
            // $event->getRequest()->attributes,
            // $event->getRequest()->attributes->get('_controller'),
            // $event->getRequest()->server,
            // $event->getRequest()->headers,
            // $event->getRequest()->headers->all(),
            // $event->getRequest()->headers->get('accept'),
            // $event->getRequest()->get('_route')

        // );

        // VÉRIFIER QU'UNE ROUTE EST RENCONTRÉE
        // if ($event->getRequest()->get('_route') === 'products') {
        //     dd('matched !');
        // }

    }

}
