<?php

namespace App\Tests\Controller\BookController;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookApiController extends WebTestCase
{
    public function testAccess(): void
    {
        $client = static::createClient([], [
            'HTTP_X_AUTH_TOKEN' => 'pass1',
        ]);

        $client->request('GET', '/api/v1/books');
        self::assertResponseIsSuccessful();
    }

    public function testNotAccess(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/books');
        self::assertResponseStatusCodeSame(403);
    }
}
