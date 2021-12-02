<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class CustomApiWebTestCase extends WebTestCase
{
    protected function getAuthorizedClient(): KernelBrowser
    {
        return static::createClient([], [
            'HTTP_X_AUTH_TOKEN' => 'pass1',
        ]);
    }
}
