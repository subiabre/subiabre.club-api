<?php

namespace App\Entity\Photo;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata as API;
use App\Repository\Photo\PhotoPortraitRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PhotoPortraitRepository::class)]
#[API\ApiResource(
    uriTemplate: '/photo/portraits',
    operations: [
        new API\GetCollection(),
        new API\Post()
    ]
)]
#[API\ApiResource(
    uriTemplate: '/photo/portraits/{id}',
    uriVariables: [
        'id' => new API\Link(
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
#[API\ApiFilter(
    filterClass: SearchFilter::class,
    properties: ['person' => 'exact']
)]
class PhotoPortrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'portraits')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull()]
    private ?PhotoImage $image = null;

    #[ORM\ManyToOne(inversedBy: 'portraits')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull()]
    private ?PhotoPerson $person = null;

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

    public function getImage(): ?PhotoImage
    {
        return $this->image;
    }

    public function setImage(?PhotoImage $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getPerson(): ?PhotoPerson
    {
        return $this->person;
    }

    public function setPerson(?PhotoPerson $person): self
    {
        $this->person = $person;

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
