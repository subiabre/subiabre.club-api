<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\AuthenticationService;
use Doctrine\ORM\EntityManagerInterface;

class UserSessionStateProcessor implements ProcessorInterface
{
    public function __construct(
        private AuthenticationService $authenticationService,
        private EntityManagerInterface $entityManagerInterface
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $this->authenticationService->invalidateSession($data->getSessionId());

        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}
