<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\UserRepository;
use App\Service\Session\SessionService;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class UserSessionTokenStateProcessor implements ProcessorInterface
{
    public function __construct(
        private SessionService $sessionService,
        private UserRepository $userRepository
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $user = $this->userRepository->find($uriVariables['id']);

        if (!$user) throw new UserNotFoundException();

        $data->setToken($this->sessionService->createUserSessionToken(
            $user,
            $this->sessionService->getConfig()[SessionService::CONFIG_SESSION_LIFETIME]
        ));
    }
}
