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
            ->orderBy('b.date_read', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findExistsBook(string $name, string $author): ?Book
    {
        return $this->findOneBy([
            'name' => $name,
            'author' => $author,
        ]);
    }

    public function findExistsBookNotId(string $name, string $author, int $notId): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.id != :id')
            ->andWhere('b.name = :name')
            ->andWhere('b.author = :author')
            ->setParameters([
                'name' => $name,
                'author' => $author,
                'id' => $notId
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    // /**
    //  * @return Book[] Returns an array of Book objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
