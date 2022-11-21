<?php

namespace App\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User\UserKey;
use App\Repository\User\UserRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class UserKeyStateProvider implements ProviderInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->userRepository->find($uriVariables['id']);

        if (!$user) throw new UserNotFoundException();

        $key = new UserKey();
        $key->setUser($user);
        $key->setDateCreated(new \DateTime());

        return $key;
    }
}
