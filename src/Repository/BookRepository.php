<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @return Book[]
     */
    public function findByReadingAll(): array
    {
        return $this->createQueryBuilder('b')
            ->addSelect('CASE WHEN b.date_read IS NULL THEN 1 ELSE 0 END as HIDDEN _sort')
            ->orderBy('_sort', 'ASC')
            ->addOrderBy('b.date_read', 'DESC')
            ->addOrderBy('b.name', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
