<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Book;
use App\Service\BookService;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class BookEntityEventSubscriber implements EventSubscriberInterface
{
    private BookService $bookService;
    private FilesystemAdapter $cache;
    private string $cacheKey;

    public function __construct(string $cacheKey, FilesystemAdapter $cache, BookService $bookService)
    {
        $this->bookService = $bookService;
        $this->cache = $cache;
        $this->cacheKey = $cacheKey;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::preRemove,
        ];
    }

    public function postPersist(LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();

        if ($entity instanceof Book) {
            $this->cache->delete($this->cacheKey);
        }
    }

    public function postUpdate(LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();

        if ($entity instanceof Book) {
            $this->cache->delete($this->cacheKey);
        }
    }

    public function preRemove(LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();

        if ($entity instanceof Book) {
            $this->bookService->removeData($entity);
            $this->cache->delete($this->cacheKey);
        }
    }
}
