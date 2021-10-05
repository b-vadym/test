<?php

declare(strict_types=1);

namespace App\Tests\Functional;

/**
 * @internal
 */
class SmokeTest extends WebTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DBRefresher::refresh();
    }

    /**
     * @dataProvider anonUserUrlProvider
     */
    public function testAnonUserPageIsSuccessful(string $uri): void
    {
        $this->client->request('GET', $uri);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @psalm-return iterable<array-key, array{uri:string}>
     */
    public function anonUserUrlProvider(): iterable
    {
        yield ['uri' => '/'];
        yield ['uri' => '/login'];
    }

    /**
     * @dataProvider authorizedUserPagesIsSuccessfulDataProvider
     */
    public function testAuthorizedUserPagesIsSuccessful(string $email, string $uri): void
    {
        $this->logIn($email);
        $this->client->request('GET', $uri);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @return iterable<array-key, array{email: string, uri: string}>
     */
    public function authorizedUserPagesIsSuccessfulDataProvider(): iterable
    {
        yield ['email' => 'v.bondarenko2991+testuser1@gmail.com', 'uri' => '/'];
    }

    /**
     * @dataProvider logoutUserEmailProvider
     */
    public function testLogout(string $email): void
    {
        $this->logIn($email);

        $this->client->request('GET', '/logout');

        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    /**
     * @psalm-return iterable<array-key, array{email: string}>
     */
    public function logoutUserEmailProvider(): iterable
    {
        yield ['email' => 'v.bondarenko2991+testuser1@gmail.com'];
    }
}
