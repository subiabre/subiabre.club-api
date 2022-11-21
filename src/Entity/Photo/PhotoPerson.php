<?php

namespace App\Entity\Photo;

use ApiPlatform\Metadata as API;
use App\Repository\Photo\PhotoPersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PhotoPersonRepository::class)]
#[UniqueEntity(fields: ['name'])]
#[API\ApiResource(
    uriTemplate: '/photo/people',
    operations: [
        new API\GetCollection(),
        new API\Post()
    ]
)]
#[API\ApiResource(
    uriTemplate: '/photo/people/{id}',
    uriVariables: [
        'id' => new API\Link(
            fromClass: PhotoPerson::class
        )
    ],
    operations: [
        new API\Get(),
        new API\Put(),
        new API\Delete(),
        new API\Patch()
    ]
)]
class PhotoPerson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: PhotoPortrait::class)]
    private Collection $portraits;

    public function __construct()
    {
        $this->portraits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, PhotoPortrait>
     */
    public function getPortraits(): Collection
    {
        return $this->portraits;
    }

    public function addPortrait(PhotoPortrait $portrait): self
    {
        if (!$this->portraits->contains($portrait)) {
            $this->portraits->add($portrait);
            $portrait->setPerson($this);
        }

        return $this;
    }

    public function removePortrait(PhotoPortrait $portrait): self
    {
        if ($this->portraits->removeElement($portrait)) {
            // set the owning side to null (unless already changed)
            if ($portrait->getPerson() === $this) {
                $portrait->setPerson(null);
            }
        }

        return $this;
    }
}
