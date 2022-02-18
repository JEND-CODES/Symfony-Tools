<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{

    public function onKernelException(ExceptionEvent $event)
    {
         //*** 1. THROW MESSAGE
        // $exception = $event->getThrowable();
        // $event->getThrowable();

        // $response = new Response('Cette page n\'existe pas');

        // $event->setResponse($response);

        //*** 2. THROW INTERNAL SERVER ERROR
        // $exception = new \Exception('Error 500');
        // $event->setThrowable($exception);

        //*** 3. THROW JSON RESPONSE
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {

            $response = new JsonResponse([
                'error' => 'not found',
                'http status' => '404',
                'message source' => 'ExceptionListener'
            ]);

        }

        $event->setResponse($response);

    }

}