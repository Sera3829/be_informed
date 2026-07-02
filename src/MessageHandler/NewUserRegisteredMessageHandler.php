<?php

namespace App\MessageHandler;

use App\Message\NewUserRegisteredMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
class NewUserRegisteredMessageHandler
{
    public function __construct(private MailerInterface $mailer) {}

    public function __invoke(NewUserRegisteredMessage $message): void
    {
        $email = (new Email())
            ->from('noreply@be-informed.com')
            ->to('admin@be-informed.com')
            ->subject('Nouvelle inscription — ' . $message->getType())
            ->html(
                '<h2>Nouvelle inscription</h2>
                <p><strong>Type :</strong> ' . $message->getType() . '</p>
                <p><strong>Nom :</strong> ' . $message->getPrenom() . ' ' . $message->getNom() . '</p>
                <p><strong>Identifiant :</strong> ' . $message->getIdentifiant() . '</p>'
            );

        $this->mailer->send($email);
    }
}
