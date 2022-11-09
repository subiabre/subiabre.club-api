<?php

namespace App\Entity;

use ApiPlatform\Metadata as API;
use ApiPlatform\Metadata\Link;
use App\Repository\UserSessionRepository;
use App\State\UserSessionStateProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserSessionRepository::class)]
#[UniqueEntity(fields: ['sessionId'])]
#[API\ApiResource(
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']],
    uriTemplate: '/users/{id}/sessions',
    uriVariables: [
        'id' => new Link(
            fromClass: User::class,
            toProperty: 'user'
        )
    ],
    operations: [
        new API\GetCollection(),
    ]
)]
#[API\ApiResource(
    uriTemplate: '/users/{id}/sessions/{sessionId}',
    uriVariables: [
        'id' => new Link(
            fromClass: User::class,
            toProperty: 'user'
        ),
        'sessionId' => new Link(
            fromClass: UserSession::class
        )
    ],
    operations: [
        new API\Get(security: "object.getUser() == user"),
        new API\Delete(security: "object.getUser() == user", processor: UserSessionStateProcessor::class)
    ]
)]
class UserSession
{
    #[Groups(['user:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['user:read'])]
    #[ORM\ManyToOne(inversedBy: 'userSessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 32)]
    private ?string $sessionId = null;

    #[Groups(['user:read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreated = null;

    #[Groups(['user:read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateExpires = null;

    #[Groups(['user:read'])]
    #[ORM\Column(length: 255)]
    private ?string $userAgent = null;

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

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $sessionId): self
    {
        $this->sessionId = $sessionId;

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

    public function getDateExpires(): ?\DateTimeInterface
    {
        return $this->dateExpires;
    }

    public function setDateExpires(\DateTimeInterface $dateExpires): self
    {
        $this->dateExpires = $dateExpires;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }
}
