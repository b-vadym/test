<?php

declare(strict_types=1);

namespace App\Controller;

use GuzzleHttp\ClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SentryController extends AbstractController
{
    private ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route(path: 'http-call', name: 'app_sentry_http_call')]
    public function httpCallAction(): Response
    {
        $this->client->request('GET', '/get');

        return new Response();
    }
}
