<?php

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\BookEntityEventSubscriber;
use App\Service\BookService;
use ReflectionProperty;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class BookEntityEventSubscriberTest extends KernelTestCase
{
    public function testRemoveBookData(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $subscriber = $container->get(BookEntityEventSubscriber::class);
        $bookService = $container->get(BookService::class);

        // Mocks
        $mockBookService = $this->createMock(BookService::class);
        $mockBookService->expects($this->once())->method('removeBookData');

        $mockFilesystemAdapter = $this->createMock(FilesystemAdapter::class);
        $mockFilesystemAdapter->expects($this->exactly(2))->method('delete');

        // Set
        $property = new ReflectionProperty($subscriber, 'bookService');
        $property->setAccessible(true);
        $property->setValue($subscriber, $mockBookService);

        $property = new ReflectionProperty($subscriber, 'cache');
        $property->setAccessible(true);
        $property->setValue($subscriber, $mockFilesystemAdapter);

        // Test
        $book = $bookService->createBookEntity();
        $bookService->addBook($book);
        $bookService->removeBook($book);
    }
}
