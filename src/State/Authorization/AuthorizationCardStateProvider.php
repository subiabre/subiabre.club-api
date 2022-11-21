<?php

namespace App\State\Authorization;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Authorization\AuthorizationCard;
use App\Repository\Authorization\AuthorizationGroupRepository;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class AuthorizationCardStateProvider implements ProviderInterface
{
    public function __construct(
        private AuthorizationGroupRepository $authorizationGroupRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $group = $this->authorizationGroupRepository->find($uriVariables['id']);

        if (!$group) throw new ResourceNotFoundException();

        $card = new AuthorizationCard();
        $card->setAuthorizationGroup($group);

        return $card;
    }
}
