<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mercure')]
class MercureController extends AbstractController
{
    #[Route('/publish')]
    public function publish(HubInterface $hub): Response
    {
        $update = new Update(
            'http://example.com/books/1',
            json_encode(['status' => 'OutOfStock'])
        );

        $hub->publish($update);

        return new Response('published!');
    }

    #[Route('/subscribe')]
    public function subscribing(HubInterface $hub): Response
    {
        return $this->render('/mercure/subscribe.html.twig', [
            'mercure_url' => $hub->getPublicUrl()
        ]);
    }
}
