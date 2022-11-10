<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserStateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManagerInterface,
        private UserPasswordHasherInterface $userPasswordHasherInterface
    ) {
    }
    
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $data->setPassword($this->userPasswordHasher->hashPassword($data, $data->getPassword()));

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }
}
