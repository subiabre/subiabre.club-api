<?php

namespace App\Entity;

use ApiPlatform\Metadata as API;
use App\Repository\PhotoPortraitRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PhotoPortraitRepository::class)]
#[API\ApiResource(
    uriTemplate: '/photos/{id}/media/{mediaId}/portraits',
    uriVariables: [
        'id' => new API\Link(
            fromClass: Photo::class,
        ),
        'mediaId' => new API\Link(
            fromClass: PhotoMedia::class,
            toClass: 'photoMedia'
        )
    ],
    operations: [
        new API\GetCollection(),
        new API\Post()
    ]
)]
#[API\ApiResource(
    uriTemplate: '/photos/{id}/media/{mediaId}/portraits/{portraitId}',
    uriVariables: [
        'id' => new API\Link(
            fromClass: Photo::class
        ),
        'mediaId' => new API\Link(
            fromClass: PhotoMedia::class,
            toProperty: 'photoMedia'
        ),
        'portraitId' => new API\Link(
            fromClass: PhotoPortrait::class
        )
    ],
    operations: [
        new API\Get(),
        new API\Put(),
        new API\Delete(),
        new API\Patch()
    ]
)]
class PhotoPortrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'photoPortraits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?People $people = null;

    #[ORM\ManyToOne(inversedBy: 'photoPortraits')]
    #[ORM\JoinColumn(nullable: false)]
    #[API\ApiProperty(writable: false)]
    private ?PhotoMedia $photoMedia = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Assert\PositiveOrZero()]
    private ?int $positionX = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Assert\PositiveOrZero()]
    private ?int $positionY = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Assert\PositiveOrZero()]
    private ?int $sizeX = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Assert\PositiveOrZero()]
    private ?int $sizeY = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPeople(): ?People
    {
        return $this->people;
    }

    public function setPeople(?People $people): self
    {
        $this->people = $people;

        return $this;
    }

    public function getPhotoMedia(): ?PhotoMedia
    {
        return $this->photoMedia;
    }

    public function setPhotoMedia(?PhotoMedia $photoMedia): self
    {
        $this->photoMedia = $photoMedia;

        return $this;
    }

    public function getPositionX(): ?int
    {
        return $this->positionX;
    }

    public function setPositionX(int $positionX): self
    {
        $this->positionX = $positionX;

        return $this;
    }

    public function getPositionY(): ?int
    {
        return $this->positionY;
    }

    public function setPositionY(int $positionY): self
    {
        $this->positionY = $positionY;

        return $this;
    }

    public function getSizeX(): ?int
    {
        return $this->sizeX;
    }

    public function setSizeX(int $sizeX): self
    {
        $this->sizeX = $sizeX;

        return $this;
    }

    public function getSizeY(): ?int
    {
        return $this->sizeY;
    }

    public function setSizeY(int $sizeY): self
    {
        $this->sizeY = $sizeY;

        return $this;
    }
}
