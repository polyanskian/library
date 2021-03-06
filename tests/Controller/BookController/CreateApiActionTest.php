<?php

declare(strict_types=1);

namespace App\Tests\Controller\BookController;

use App\Repository\BookRepository;
use App\Tests\CustomApiWebTestCase;

class CreateApiActionTest extends CustomApiWebTestCase
{
    private const URL = '/api/v1/books/add';

    public function testErrorFieldDataType(): void
    {
        $data = [
            'name' => 'test',
            'author' => 'test',
            'is_download' => null,
        ];

        $expect = [
            'success' => false,
            'message' => 'Param `Book.is_download` type not boolean',
        ];

        $client = $this->getAuthorizedClient();
        $client->jsonRequest('POST', self::URL, $data);
        $responce = json_decode($client->getResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400);
        self::assertSame($expect, $responce);
    }

    public function testOk(): void
    {
        $expect = [
            'name' => 'test',
            'author' => 'test',
            'cover' => '',
            'file' => '',
            'is_download' => false,
        ];

        $client = $this->getAuthorizedClient();
        $client->jsonRequest('POST', self::URL, $expect);
        $responce = json_decode($client->getResponse()->getContent(), true);

        self::assertResponseIsSuccessful();
        self::assertArrayHasKey('id', $responce);

        unset($responce['id']);
        self::assertSame($expect, $responce);

        $repository = static::getContainer()->get(BookRepository::class);

        $book = $repository->findOneBy([
            'name' => 'test',
            'author' => 'test',
        ]);

        self::assertNotNull($book);
    }

    /**
     * @dataProvider providerErrorData
     */
    public function testErrorFieldName(array $data, string $expect): void
    {
        $client = $this->getAuthorizedClient();
        $client->jsonRequest('POST', self::URL, $data);
        $responce = json_decode($client->getResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(422);
        $result = $responce['detail'] ?? '';
        self::assertSame($expect, $result);
    }

    public function providerErrorData(): array
    {
        return [
            [
                ['author' => 'test'],
                'name: ???????????????? ???? ???????????? ???????? ????????????.'
            ],
            [
                ['name' => 'test'],
                'author: ???????????????? ???? ???????????? ???????? ????????????.'
            ],
            [
                [],
                "name: ???????????????? ???? ???????????? ???????? ????????????.\nauthor: ???????????????? ???? ???????????? ???????? ????????????."
            ],
        ];
    }
}
