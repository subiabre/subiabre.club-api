<?php

namespace App\Entity;

use ApiPlatform\Metadata as API;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\Repository\UserKeyRepository;
use App\State\UserKeyStateProcessor;
use App\State\UserKeyStateProvider;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserKeyRepository::class)]
#[API\ApiResource(
    uriTemplate: '/users/{id}/keys',
    uriVariables: [
        'id' => new Link(
            fromClass: User::class,
            toProperty: 'user'
        )
    ],
    operations: [
        new GetCollection(security: "is_granted('USER_IS', request)"),
        new Post(
            provider: UserKeyStateProvider::class,
            processor: UserKeyStateProcessor::class,
            security: "is_granted('USER_IS', request)"
        )
    ]
)]
#[API\ApiResource(
    uriTemplate: '/users/{id}/keys/{keyId}',
    uriVariables: [
        'id' => new Link(
            fromClass: User::class,
            toProperty: 'user'
        ),
        'keyId' => new Link(
            fromClass: UserKey::class
        )
    ],
    operations: [
        new Delete(
            security: "is_granted('USER_IS', request)"
        )
    ]
)]
class UserKey
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userKeys')]
    #[ORM\JoinColumn(nullable: false)]
    #[API\ApiProperty(writable: false, readable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 64)]
    #[API\ApiProperty(writable: false)]
    private ?string $value = null;

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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
