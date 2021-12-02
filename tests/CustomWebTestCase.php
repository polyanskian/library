<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class CustomWebTestCase extends WebTestCase
{
    protected function getAuthorizedClient(): KernelBrowser
    {
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $repository = $em->getRepository(User::class);
        $user = $repository->findOneBy(['username' => 'test']);

        return $client->loginUser($user);
    }
}
