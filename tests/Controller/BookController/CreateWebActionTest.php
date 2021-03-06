<?php

declare(strict_types=1);

namespace App\Tests\Controller\BookController;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Tests\CustomWebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;

class CreateWebActionTest extends CustomWebTestCase
{
    private const URL = '/book/new';

    public function testOk(): void
    {
        $client = $this->getAuthorizedClient();
        $crawler = $client->request('GET', self::URL);
        $form = $this->getForm($crawler);

        $client->submit($form, [
            'book[name]' => 'test',
            'book[author]' => 'test',
            'book[cover]' => __DIR__ . '/uploads/cover.jpg',
            'book[file]' => __DIR__ . '/uploads/book.txt',
        ]);

        self::assertResponseStatusCodeSame(303);

        $location = $client->getResponse()->headers->get('location');
        self::assertMatchesRegularExpression('#/book/\d+/edit#', $location);

        $repository = static::getContainer()->get(BookRepository::class);

        $book = $repository->findOneBy([
           'name' => 'test',
           'author' => 'test',
        ]);

        self::assertNotNull($book);
    }

    public function testErrorBookUnique(): void
    {
        $code = 422;
        $errorSelector = 'input.is-invalid[name="book[name]"]~.invalid-feedback.d-block';

        $client = $this->getAuthorizedClient();
        $crawler = $client->request('GET', self::URL);
        $form = $this->getForm($crawler);
        $em = static::getContainer()->get(EntityManagerInterface::class);

        $book = (new Book())
            ->setName('test')
            ->setAuthor('test')
            ->setIsDownload(true)
        ;

        $em->persist($book);
        $em->flush();

        $client->submit($form, [
            'book[name]' => 'test',
            'book[author]' => 'test',
            'book[cover]' => __DIR__ . '/uploads/cover.jpg',
            'book[file]' => __DIR__ . '/uploads/book.txt',
        ]);

        self::assertResponseStatusCodeSame($code);
        self::assertSelectorTextSame($errorSelector, '?????? ???????????????? ?????? ????????????????????????.');
    }

    public function testErrorFieldFile(): void
    {
        $code = 422;
        $errorSelector = 'input.is-invalid[name="book[file]"]~.invalid-feedback.d-block';

        $client = $this->getAuthorizedClient();
        $crawler = $client->request('GET', self::URL);
        $form = $this->getForm($crawler);

        $client->submit($form);

        self::assertResponseStatusCodeSame($code);
        self::assertSelectorNotExists($errorSelector);

        $client->submit($form, [
            'book[file]' => __DIR__ . '/uploads/cover.jpg'
        ]);

        self::assertResponseStatusCodeSame($code);
        self::assertSelectorTextSame($errorSelector, '?????????????????? ?????????????? ?????? ????????????????: epub, txt');

        $client->submit($form, [
            'book[file]' => __DIR__ . '/uploads/book-empty.txt'
        ]);

        self::assertResponseStatusCodeSame($code);
        self::assertSelectorTextSame($errorSelector, '???????????? ?????????? ???? ??????????????????.');

        // ?????? ?????????? ???? ???????????????????? ???????????? ??????????
    }

    public function testErrorFieldCover(): void
    {
        $code = 422;
        $errorSelector = 'input.is-invalid[name="book[cover]"]~.invalid-feedback.d-block';

        $client = $this->getAuthorizedClient();
        $crawler = $client->request('GET', self::URL);
        $form = $this->getForm($crawler);

        $client->submit($form);

        self::assertResponseStatusCodeSame($code);
        self::assertSelectorNotExists($errorSelector);

        $client->submit($form, [
            'book[cover]' => __DIR__ . '/uploads/book.txt'
        ]);

        self::assertResponseStatusCodeSame($code);
        self::assertSelectorTextSame($errorSelector, '?????????????????? ?????????????? ?????? ????????????????: jpg, png');

        $client->submit($form, [
            'book[cover]' => __DIR__ . '/uploads/book-empty.txt'
        ]);

        self::assertResponseStatusCodeSame($code);
        self::assertSelectorTextSame($errorSelector, '???????????? ?????????? ???? ??????????????????.');

        // ?????? ?????????? ???? ???????????????????? ???????????? ??????????
    }

    public function testErrorFieldAuthor(): void
    {
        $code = 422;
        $errorSelector = 'input.is-invalid[name="book[author]"]~.invalid-feedback.d-block';

        $client = $this->getAuthorizedClient();
        $crawler = $client->request('GET', self::URL);
        $form = $this->getForm($crawler);

        $client->submit($form);

        self::assertResponseStatusCodeSame($code);
        self::assertSelectorTextSame($errorSelector, '???????????????? ???? ???????????? ???????? ????????????.');

        $client->submit($form, [
            'book[author]' => 'test'
        ]);

        self::assertResponseStatusCodeSame($code);
        self::assertSelectorNotExists($errorSelector);
    }

    public function testErrorFieldName(): void
    {
        $code = 422;
        $errorSelector = 'input.is-invalid[name="book[name]"]~.invalid-feedback.d-block';

        $client = $this->getAuthorizedClient();
        $crawler = $client->request('GET', self::URL);
        $form = $this->getForm($crawler);

        $client->submit($form);

        self::assertResponseStatusCodeSame($code);
        self::assertSelectorTextSame($errorSelector, '???????????????? ???? ???????????? ???????? ????????????.');

        $client->submit($form, [
           'book[name]' => 'test'
        ]);

        self::assertResponseStatusCodeSame($code);
        self::assertSelectorNotExists($errorSelector);
    }

    private function getForm(Crawler $crawler): Form
    {
        return $crawler->filter('form[name="book"]')
            ->selectButton('_submit')
            ->form();
    }
}
