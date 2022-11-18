<?php

namespace App\Entity;

use ApiPlatform\Metadata as API;
use App\Repository\PeopleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PeopleRepository::class)]
#[API\ApiResource]
class People
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'people', targetEntity: PhotoPortrait::class)]
    #[Assert\NotNull()]
    private Collection $photoPortraits;

    public function __construct()
    {
        $this->photoPortraits = new ArrayCollection();
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
    public function getPhotoPortraits(): Collection
    {
        return $this->photoPortraits;
    }

    public function addPhotoPortrait(PhotoPortrait $photoPortrait): self
    {
        if (!$this->photoPortraits->contains($photoPortrait)) {
            $this->photoPortraits->add($photoPortrait);
            $photoPortrait->setPeople($this);
        }

        return $this;
    }

    public function removePhotoPortrait(PhotoPortrait $photoPortrait): self
    {
        if ($this->photoPortraits->removeElement($photoPortrait)) {
            // set the owning side to null (unless already changed)
            if ($photoPortrait->getPeople() === $this) {
                $photoPortrait->setPeople(null);
            }
        }

        return $this;
    }
}
