<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FlashService
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function notice(string $message): self
    {
        $this->session->getFlashBag()->add('secondary', $message);
        return $this;
    }

    public function ok(string $message): self
    {
        $this->session->getFlashBag()->add('success', $message);
        return $this;
    }

    public function info(string $message): self
    {
        $this->session->getFlashBag()->add('info', $message);
        return $this;
    }

    public function warning(string $message): self
    {
        $this->session->getFlashBag()->add('warning', $message);
        return $this;
    }

    public function error(string $message): self
    {
        $this->session->getFlashBag()->add('danger', $message);
        return $this;
    }
}
