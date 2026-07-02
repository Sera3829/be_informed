<?php

namespace App\Security\Voter;

use App\Entity\Conference;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ConferenceVoter extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';
    const VIEW = 'view';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE, self::VIEW])
            && $subject instanceof Conference;
    }

    protected function voteOnattribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Conference $conference */
        $conference = $subject;

        return match ($attribute) {
            self::VIEW => true, // tout le monde peut voir
            self::EDIT => $this->canEdit($conference, $user),
            self::DELETE => $this->canDelete($conference, $user),
            default => false,
        };
    }

    private function canEdit(Conference $conference, User $user): bool
    {
        // Admin peut tout modifier
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Conférencier peut modifier uniquement ses conférences
        return $conference->getOwner() === $user;
    }

    private function canDelete(Conference $conference, User $user): bool
    {
        // Même logique que edit
        return $this->canEdit($conference, $user);
    }
}
