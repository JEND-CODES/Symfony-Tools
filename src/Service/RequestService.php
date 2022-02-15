<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class RequestService
{
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getRouteName()
    {
        $routeName = $this->requestStack->getCurrentRequest()->get('_route');

        return $routeName;
    }

    public function getUriInfo()
    {
        $uriInfo = $this->requestStack->getCurrentRequest()->getUri();

        return $uriInfo;
    }

    public function getPortInfo()
    {
        $portInfo = $this->requestStack->getCurrentRequest()->getPort();

        return $portInfo;
    }

    public function getController()
    {
        $controller = $this->requestStack->getCurrentRequest()->get('_controller');

        return $controller;
    }

    public function getSessionInfos()
    {
        $session = $this->requestStack->getsession();

        return $session;
    }

}