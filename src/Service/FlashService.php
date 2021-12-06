<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class FlashService
{
    private FlashBagInterface $bag;

    public function __construct(FlashBagInterface $bag)
    {
        $this->bag = $bag;
    }

    public function notice(string $message): self
    {
        $this->bag->add('secondary', $message);
        return $this;
    }
    
    public function ok(string $message): self
    {
        $this->bag->add('success', $message);
        return $this;
    }

    public function info(string $message): self
    {
        $this->bag->add('info', $message);
        return $this;
    }

    public function warning(string $message): self
    {
        $this->bag->add('warning', $message);
        return $this;
    }

    public function error(string $message): self
    {
        $this->bag->add('danger', $message);
        return $this;
    }
}
