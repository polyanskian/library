<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class Book extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 10; $i++) {
            $book = (new \App\Entity\Book())
                ->setName("Book $i $faker->firstName")
                ->setAuthor($faker->lastName)
                ->setDateRead($faker->dateTime)
                ->setIsDownload($faker->boolean)
            ;

            $manager->persist($book);
        }

        $manager->flush();
    }
}
