<?php

namespace App\Tests\Controller\BookController;

use App\Tests\CustomApiWebTestCase;

class BookApiControllerTest extends CustomApiWebTestCase
{
    private const URL = '/api/v1/books';

    public function testAccess(): void
    {
        $client = $this->getAuthorizedClient();
        $client->request('GET', self::URL);
        self::assertResponseIsSuccessful();
    }

    public function testNotAccess(): void
    {
        $client = static::createClient();
        $client->request('GET', self::URL);
        self::assertResponseStatusCodeSame(403);
    }
}
