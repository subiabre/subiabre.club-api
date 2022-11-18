<?php

namespace App\Entity;

use ApiPlatform\Metadata as API;
use App\Repository\PhotoMediaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PhotoMediaRepository::class)]
#[UniqueEntity(fields: ['location'])]
#[API\ApiResource(
    uriTemplate: '/photos/{id}/media',
    uriVariables: [
        'id' => new API\Link(
            fromClass: Photo::class,
            toProperty: 'photo'
        )
    ],
    operations: [
        new API\GetCollection(),
        new API\Post()
    ]
)]
#[API\ApiResource(
    uriTemplate: '/photos/{id}/media/{mediaId}',
    uriVariables: [
        'id' => new API\Link(
            fromClass: Photo::class,
            toProperty: 'photo'
        ),
        'mediaId' => new API\Link(
            fromClass: PhotoMedia::class
        )
    ],
    operations: [
        new API\Get(),
        new API\Put(),
        new API\Delete(),
        new API\Patch()
    ]
)]
class PhotoMedia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank()]
    #[API\ApiProperty(description: 'The path to the media image file')]
    private ?string $location = null;

    #[ORM\ManyToOne(inversedBy: 'photoMedia')]
    #[ORM\JoinColumn(nullable: false)]
    #[API\ApiProperty(writable: false)]
    private ?Photo $photo = null;

    #[ORM\OneToMany(mappedBy: 'photoMedia', targetEntity: PhotoPortrait::class, orphanRemoval: true)]
    #[API\ApiProperty(writable: false)]
    private Collection $photoPortraits;

    public function __construct()
    {
        $this->photoPortraits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    public function setPhoto(?Photo $photo): self
    {
        $this->photo = $photo;

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
            $photoPortrait->setPhotoMedia($this);
        }

        return $this;
    }

    public function removePhotoPortrait(PhotoPortrait $photoPortrait): self
    {
        if ($this->photoPortraits->removeElement($photoPortrait)) {
            // set the owning side to null (unless already changed)
            if ($photoPortrait->getPhotoMedia() === $this) {
                $photoPortrait->setPhotoMedia(null);
            }
        }

        return $this;
    }
}
