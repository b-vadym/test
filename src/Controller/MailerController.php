<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/email')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $email = (new TemplatedEmail())
            ->from('v.bondarenko2991+testfrom@gmail.com')
            ->to('v.bondarenko2991+testto@gmail.com')
            ->cc('v.bondarenko2991+cc@gmial.com')
            ->bcc('v.bondarenko2991+bcc@gmial.com')
            ->replyTo('v.bondarenko2991+replyTo@gmial.com')
            ->subject('Test subject')
            ->htmlTemplate('emails/test.html.twig')
            ->context([
                'expiration_date' => new \DateTime('+7 days'),
                'username' => 'foo',
            ])
        ;

        $mailer->send($email);

        return new JsonResponse(['status' => 'ok']);
    }
}
