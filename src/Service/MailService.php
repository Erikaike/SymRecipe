<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailService

{
    private MailerInterface $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEMail(string $from, string $render, array $context, array $form): void
    {
        $email = (new \Symfony\Component\Mime\Email())
            ->from($from)
            ->to($form->get('email')->getData())
            ->subject('your password resetting next step :D')
            ->html($render);

        $this->mailer->send($email);
    }
}