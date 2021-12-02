<?php

declare(strict_types=1);

namespace App\Tests\Controller\BookController;

use App\Tests\CustomWebTestCase;

class BookControllerTest extends CustomWebTestCase
{
    private const URL = '/book/new';

    public function testOkAccess(): void
    {
        $client = $this->getAuthorizedClient();
        $client->request('GET', self::URL);
        self::assertResponseIsSuccessful();
    }

    public function testErrorAccess(): void
    {
        $client = static::createClient();
        $client->request('GET', self::URL);
        self::assertResponseRedirects('http://localhost/login', 302);
    }
}
