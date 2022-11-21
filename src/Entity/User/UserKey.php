<?php

namespace App\Entity\User;

use ApiPlatform\Metadata as API;
use App\Repository\User\UserKeyRepository;
use App\State\User\UserKeyStateProcessor;
use App\State\User\UserKeyStateProvider;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserKeyRepository::class)]
#[API\ApiResource(
    uriTemplate: '/users/{id}/keys',
    uriVariables: [
        'id' => new API\Link(
            fromClass: User::class,
            toProperty: 'user'
        )
    ],
    operations: [
        new API\GetCollection(security: "is_granted('REQUEST_USER_IS_IN', request)"),
        new API\Post(
            provider: UserKeyStateProvider::class,
            processor: UserKeyStateProcessor::class,
            security: "is_granted('REQUEST_USER_IS_IN', request)"
        )
    ]
)]
#[API\ApiResource(
    uriTemplate: '/users/{id}/keys/{keyId}',
    uriVariables: [
        'id' => new API\Link(
            fromClass: User::class,
            toProperty: 'user'
        ),
        'keyId' => new API\Link(
            fromClass: UserKey::class
        )
    ],
    operations: [
        new API\Get(),
        new API\Delete(
            security: "is_granted('REQUEST_USER_IS_IN', request)"
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
    #[API\ApiProperty(writable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 64)]
    #[API\ApiProperty(writable: false)]
    private ?string $value = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[API\ApiProperty(writable: false)]
    private ?\DateTimeInterface $dateCreated = null;

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

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }
}
