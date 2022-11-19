<?php

namespace App\Security\Voter;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRequestVoter extends Voter
{
    public const REQUEST_USER_IS_IN = 'REQUEST_USER_IS_IN';

    public function __construct(
        private Security $security,
        private UserRepository $userRepository
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::REQUEST_USER_IS_IN])
            && ($subject instanceof Request );
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::REQUEST_USER_IS_IN:
                /** @var Request */
                $request = $subject;

                $specifiedId = (int) ($request->attributes->get('_route_params')['userId'] ?? $request->attributes->get('_route_params')['id']);
                return $this->userRepository->findByUser($this->security->getUser())?->getId() === $specifiedId;
                break;
        }

        return false;
    }
}
