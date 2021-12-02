<?php

declare(strict_types=1);

namespace App\Tests\Controller\BookController;

use App\Tests\CustomWebTestCase;

class BookControllerTest extends CustomWebTestCase
{
    public function testOkAccess(): void
    {
        $client = $this->getAuthorizedClient();
        $client->request('GET', '/book/new');
        self::assertResponseIsSuccessful();
    }

    public function testErrorAccess(): void
    {
        $client = static::createClient();
        $client->request('GET', '/book/new');
        self::assertResponseRedirects('http://localhost/login', 302);
    }
}
