<?php

declare(strict_types=1);

namespace App\Tests;

use App\DataFixtures\UserFixture;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class CustomWebTestCase extends WebTestCase
{
    public function loadFixture(string $classFixture): self
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $fixture = new $classFixture();
        $fixture->load($em);
        return $this;
    }

    protected function getAuthorizedClient(): KernelBrowser
    {
        $client = static::createClient();
        $this->loadFixture(UserFixture::class);

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $repository = $em->getRepository(User::class);
        $user = $repository->findOneBy(['username' => 'test']);

        return $client->loginUser($user);
    }
}
