<?php

declare(strict_types=1);

namespace App\Controller;

use GuzzleHttp\ClientInterface;
use Sentry\State\HubInterface;
use Sentry\Tracing\SpanContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SentryController extends AbstractController
{
    private ClientInterface $client;

    private HubInterface $hub;

    public function __construct(ClientInterface $client, HubInterface $hub)
    {
        $this->client = $client;
        $this->hub = $hub;
    }

    #[Route(path: 'http-call', name: 'app_sentry_http_call')]
    public function httpCallAction(): Response
    {
        $transactionContext = new \Sentry\Tracing\TransactionContext();
        $transactionContext->setName('External Call');
        $transactionContext->setOp('http.caller');
        $transaction = \Sentry\startTransaction($transactionContext);

        $spanContext = new \Sentry\Tracing\SpanContext();
        $spanContext->setOp('functionX');
        $span1 = $transaction->startChild($spanContext);

        $embededTransaction = $this->hub->getTransaction();

        if ($embededTransaction !== null) {
            $embededSpanContext = new SpanContext();
            $embededSpanContext->setOp('custom_request');
            $embededSpanContext->setDescription('Custom request');

            $embededSpan = $embededTransaction->startChild($embededSpanContext);
        }

        // Calling functionX
        $this->client->request('GET', '/get');
        $span1->finish();
        $transaction->finish();

        if (isset($embededSpan)) {
            $embededSpan->finish();
        }

        return new Response();
    }
}
