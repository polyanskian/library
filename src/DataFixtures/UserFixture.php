<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = (new User())
            ->setUsername('test')
            ->setEmail('test@test.test')
            ->setPlainPassword('1234567890')
            ->setEnabled(true)
            ->setRoles(['ROLE_ADMIN'])
        ;

        $manager->persist($user);
        $manager->flush();
    }
}
