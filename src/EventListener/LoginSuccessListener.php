<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

#[AsEventListener(event: LoginSuccessEvent::class)]
class LoginSuccessListener
{
    public function __construct(private EntityManagerInterface $em) {}

    public function __invoke(LoginSuccessEvent $event): void
    {
        $user = $event->getAuthenticatedToken()->getUser();

        if (!$user instanceof User) {
            return; // On ignore les PublicUser
        }

        $user->setLastLoginAt(new \DateTimeImmutable());
        $this->em->flush();
    }
}
