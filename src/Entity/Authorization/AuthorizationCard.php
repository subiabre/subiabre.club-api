<?php

namespace App\Entity\Authorization;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata as API;
use App\Entity\User\User;
use App\Repository\Authorization\AuthorizationCardRepository;
use App\State\Authorization\AuthorizationCardStateProvider;
use App\Validator\Authorization\UniqueUserInGroup;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AuthorizationCardRepository::class)]
#[UniqueUserInGroup]
#[API\ApiResource(
    routePrefix: '/authorization',
    uriTemplate: '/groups/{id}/cards',
    uriVariables: [
        'id' => new API\Link(
            fromClass: AuthorizationGroup::class,
            toProperty: 'authorizationGroup'
        )
    ],
    operations: [
        new API\GetCollection(),
        new API\Post(provider: AuthorizationCardStateProvider::class)
    ]
)]
#[API\ApiResource(
    routePrefix: '/authorization',
    uriTemplate: '/groups/{id}/cards/{cardId}',
    uriVariables: [
        'id' => new API\Link(
            fromClass: AuthorizationGroup::class,
            toProperty: 'authorizationGroup'
        ),
        'cardId' => new API\Link(fromClass: AuthorizationCard::class)
    ],
    operations: [
        new API\Get(),
        new API\Put(),
        new API\Delete(),
        new API\Patch()
    ]
)]
#[API\ApiFilter(
    filterClass: SearchFilter::class,
    properties: ['user.username' => 'partial']
)]
class AuthorizationCard
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull()]
    private ?User $user = null;

    #[ORM\Column(type: Types::JSON)]
    #[Assert\Type('array')]
    #[Assert\All(
        new Assert\Choice(
            choices: ['VIEW', 'EDIT'],
            message: 'The value {{ value }} does not match with any of the valid options: {{ choices }}'
        )
    )]
    private array $authorizations = [];

    #[ORM\ManyToOne(inversedBy: 'authorizationCards')]
    #[ORM\JoinColumn(nullable: false)]
    #[API\ApiProperty(writable: false)]
    private ?AuthorizationGroup $authorizationGroup = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAuthorizations(): array
    {
        return $this->authorizations;
    }

    public function setAuthorizations(array $authorizations): self
    {
        $this->authorizations = $authorizations;

        return $this;
    }

    public function getAuthorizationGroup(): ?AuthorizationGroup
    {
        return $this->authorizationGroup;
    }

    public function setAuthorizationGroup(?AuthorizationGroup $authorizationGroup): self
    {
        $this->authorizationGroup = $authorizationGroup;

        return $this;
    }
}
