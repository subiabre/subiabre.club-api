<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\UserRepository;
use App\Service\AuthenticationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class UserKeyStateProcessor implements ProcessorInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private AuthenticationService $authenticationService,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $user = $this->userRepository->find($uriVariables['id']);

        if (!$user) throw new UserNotFoundException();

        $data->setUser($user);
        $data->setValue($this->authenticationService->hashWithSecret());

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
