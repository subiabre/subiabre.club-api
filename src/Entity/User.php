<?php

namespace App\Entity;

use ApiPlatform\Metadata as API;
use App\Repository\UserRepository;
use App\State\UserStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'])]
#[API\ApiResource(
    operations: [
        new API\GetCollection(),
        new API\Post(processor: UserStateProcessor::class),
        new API\Get(),
        new API\Put(security: "object.getId() == user.getId()"),
        new API\Delete(security: "object.getId() == user.getId()"),
        new API\Patch(security: "object.getId() == user.getId()")
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[API\ApiProperty(writable: false)]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank()]
    private ?string $username = null;

    #[ORM\Column]
    #[API\ApiProperty(readable: false, writable: false)]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[API\ApiProperty(readable: false)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 4)]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserSession::class, orphanRemoval: true)]
    #[API\ApiProperty(writable: false)]
    private Collection $userSessions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserKey::class, orphanRemoval: true)]
    #[API\ApiProperty(writable: false, readable: false)]
    private Collection $userKeys;

    public function __construct()
    {
        $this->userSessions = new ArrayCollection();
        $this->userKeys = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, UserSession>
     */
    public function getUserSessions(): Collection
    {
        return $this->userSessions;
    }

    public function addUserSession(UserSession $userSession): self
    {
        if (!$this->userSessions->contains($userSession)) {
            $this->userSessions->add($userSession);
            $userSession->setUser($this);
        }

        return $this;
    }

    public function removeUserSession(UserSession $userSession): self
    {
        if ($this->userSessions->removeElement($userSession)) {
            // set the owning side to null (unless already changed)
            if ($userSession->getUser() === $this) {
                $userSession->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserKey>
     */
    public function getUserKeys(): Collection
    {
        return $this->userKeys;
    }

    public function addUserKey(UserKey $userKey): self
    {
        if (!$this->userKeys->contains($userKey)) {
            $this->userKeys->add($userKey);
            $userKey->setUser($this);
        }

        return $this;
    }

    public function removeUserKey(UserKey $userKey): self
    {
        if ($this->userKeys->removeElement($userKey)) {
            // set the owning side to null (unless already changed)
            if ($userKey->getUser() === $this) {
                $userKey->setUser(null);
            }
        }

        return $this;
    }
}
