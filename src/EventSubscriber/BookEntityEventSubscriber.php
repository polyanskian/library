<?php

namespace App\EventSubscriber;

use App\Entity\Book;
use App\Service\BookService;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class BookEntityEventSubscriber implements EventSubscriberInterface
{
    private BookService $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    public function preRemove(LifecycleEventArgs $event)
    {
        $entity = $event->getObject();

        if ($entity instanceof Book) {
            $this->bookService->removeData($entity);
        }
    }

    public function getSubscribedEvents(): array
    {
        return [Events::preRemove];
    }
}
