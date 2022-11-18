<?php

namespace App\Entity;

use ApiPlatform\Metadata as API;
use App\Repository\PhotoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
#[API\ApiResource]
class Photo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'photos')]
    private ?Place $place = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'photo', targetEntity: PhotoMedia::class)]
    private Collection $photoMedia;

    public function __construct()
    {
        $this->photoMedia = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, PhotoMedia>
     */
    public function getPhotoMedia(): Collection
    {
        return $this->photoMedia;
    }

    public function addPhotoMedium(PhotoMedia $photoMedium): self
    {
        if (!$this->photoMedia->contains($photoMedium)) {
            $this->photoMedia->add($photoMedium);
            $photoMedium->setPhoto($this);
        }

        return $this;
    }

    public function removePhotoMedium(PhotoMedia $photoMedium): self
    {
        if ($this->photoMedia->removeElement($photoMedium)) {
            // set the owning side to null (unless already changed)
            if ($photoMedium->getPhoto() === $this) {
                $photoMedium->setPhoto(null);
            }
        }

        return $this;
    }
}
