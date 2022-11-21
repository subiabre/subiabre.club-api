<?php

namespace App\Entity\User;

use ApiPlatform\Metadata as API;
use App\Repository\User\UserSessionRepository;
use App\State\User\UserSessionStateProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserSessionRepository::class)]
#[UniqueEntity(fields: ['sessionId'])]
#[API\ApiResource(
    uriTemplate: '/users/{id}/sessions',
    uriVariables: [
        'id' => new API\Link(
            fromClass: User::class,
            toProperty: 'user'
        )
    ],
    operations: [
        new API\GetCollection(security: "is_granted('REQUEST_USER_IS_IN', request)"),
    ]
)]
#[API\ApiResource(
    uriTemplate: '/users/{id}/sessions/{sessionId}',
    uriVariables: [
        'id' => new API\Link(
            fromClass: User::class,
            toProperty: 'user'
        ),
        'sessionId' => new API\Link(
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
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userSessions')]
    #[ORM\JoinColumn(nullable: false)]
    #[API\ApiProperty(writable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 32)]
    #[API\ApiProperty(readable: false, writable: false)]
    private ?string $sessionId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[API\ApiProperty(writable: false)]
    private ?\DateTimeInterface $dateCreated = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[API\ApiProperty(writable: false)]
    private ?\DateTimeInterface $dateExpires = null;

    #[ORM\Column(length: 255)]
    #[API\ApiProperty(writable: false)]
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
