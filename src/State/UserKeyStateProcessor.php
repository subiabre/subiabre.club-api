<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\AuthenticationService;
use Doctrine\ORM\EntityManagerInterface;

class UserKeyStateProcessor implements ProcessorInterface
{
    public function __construct(
        private AuthenticationService $authenticationService,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $data = $this->authenticationService->updateUserKeyValue($data);

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
