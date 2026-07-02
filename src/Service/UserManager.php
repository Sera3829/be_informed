<?php

namespace App\Service;

use App\Entity\PublicUser;
use App\Entity\User;
use App\Message\NewUserRegisteredMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private MessageBusInterface $bus
    ) {}

    public function createUser(User $user, string $plainPassword, array $roles): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        $user->setRoles($roles);
        $user->setCreatedAt(new \DateTimeImmutable());
        $this->em->persist($user);
        $this->em->flush();

        $this->bus->dispatch(new NewUserRegisteredMessage(
            $user->getNom(),
            $user->getPrenom(),
            $user->getEmail(),
            in_array('ROLE_ADMIN', $roles) ? 'Admin' : 'Conférencier'
        ));
    }

    public function createPublicUser(PublicUser $user, string $plainPassword): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        $user->setCreatedAt(new \DateTimeImmutable());
        $this->em->persist($user);
        $this->em->flush();

        $this->bus->dispatch(new NewUserRegisteredMessage(
            $user->getNom(),
            $user->getPrenom(),
            $user->getPhone(),
            'Visiteur'
        ));
    }

    public function updatePassword(User|PublicUser $user, string $plainPassword): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        $this->em->flush();
    }
}
