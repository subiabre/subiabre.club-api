<?php

namespace App\Entity\Authorization;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata as API;
use App\Repository\Authorization\AuthorizationGroupRepository;
use App\Validator\Authorization\UniqueUserInGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AuthorizationGroupRepository::class)]
#[UniqueEntity(fields: ['title'])]
#[API\ApiResource(
    routePrefix: '/authorization',
    uriTemplate: '/groups',
    operations: [
        new API\GetCollection(),
        new API\Post()
    ]
)]
#[API\ApiResource(
    routePrefix: '/authorization',
    uriTemplate: '/groups/{id}',
    uriVariables: [
        'id' => new API\Link(fromClass: AuthorizationGroup::class)
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
    properties: ['title' => 'partial']
)]
#[API\ApiFilter(
    filterClass: BooleanFilter::class,
    properties: ['public']
)]
class AuthorizationGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $title = null;

    #[ORM\Column]
    #[Assert\Type('boolean')]
    private bool $public = false;

    #[ORM\OneToMany(mappedBy: 'authorizationGroup', targetEntity: AuthorizationCard::class, orphanRemoval: true)]
    #[API\ApiProperty(writable: false)]
    private Collection $authorizationCards;

    public function __construct()
    {
        $this->authorizationCards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
    }

    /**
     * @return Collection<int, AuthorizationCard>
     */
    public function getAuthorizationCards(): Collection
    {
        return $this->authorizationCards;
    }

    public function addAuthorizationCard(AuthorizationCard $authorizationCard): self
    {
        if (!$this->authorizationCards->contains($authorizationCard)) {
            $this->authorizationCards->add($authorizationCard);
            $authorizationCard->setAuthorizationGroup($this);
        }

        return $this;
    }

    public function removeAuthorizationCard(AuthorizationCard $authorizationCard): self
    {
        if ($this->authorizationCards->removeElement($authorizationCard)) {
            // set the owning side to null (unless already changed)
            if ($authorizationCard->getAuthorizationGroup() === $this) {
                $authorizationCard->setAuthorizationGroup(null);
            }
        }

        return $this;
    }
}
