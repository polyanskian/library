<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FlashService
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function notice(string $message)
    {
        $this->session->getFlashBag()->add('secondary', $message);
    }

    public function ok(string $message)
    {
        $this->session->getFlashBag()->add('success', $message);
    }

    public function info(string $message)
    {
        $this->session->getFlashBag()->add('info', $message);
    }

    public function warning(string $message)
    {
        $this->session->getFlashBag()->add('warning', $message);
    }

    public function error(string $message)
    {
        $this->session->getFlashBag()->add('danger', $message);
    }
}
