<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

/**
 * @internal
 */
abstract class WebTestCase extends BaseWebTestCase
{
    /**
     * @var KernelBrowser
     */
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->client = null;
        gc_collect_cycles();
    }

    protected function getHost(): string
    {
        return 'localhost';
    }

    protected function logIn(string $email): void
    {
        $user = $this->getUserByEmail($email);
        $this->client->loginUser($user);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        /** @var EntityManagerInterface */
        return self::getContainer()->get('doctrine.orm.entity_manager');
    }

    protected function getUserByEmail(string $email): User
    {
        $em = $this->getEntityManager();
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        $this->assertNotNull($user);

        return $user;
    }
}
