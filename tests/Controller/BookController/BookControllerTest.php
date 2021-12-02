<?php

declare(strict_types=1);

namespace App\Tests\Controller\BookController;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    public function testOkAccess(): void
    {
        $client = $this->getAuthorizedClient();
        $client->request('GET', '/book/new');
        self::assertResponseIsSuccessful();
    }

    public function testErrorAccess(): void
    {
        $client = static::createClient();
        $client->request('GET', '/book/new');
        self::assertResponseRedirects('http://localhost/login', 302);
    }

    private function getAuthorizedClient(): KernelBrowser
    {
        $client = static::createClient();

        $em = static::getContainer()->get(EntityManagerInterface::class);
        $repository = $em->getRepository(User::class);
        $user = $repository->findOneBy(['username' => 'test']);

        return $client->loginUser($user);
    }
}
