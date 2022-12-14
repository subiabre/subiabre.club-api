<?php

namespace App\Entity\Photo;

use ApiPlatform\Metadata as API;
use App\Repository\Photo\PhotoLocationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PhotoLocationRepository::class)]
#[UniqueEntity(fields: ['name'])]
#[API\ApiResource(
    uriTemplate: '/photo/locations',
    operations: [
        new API\GetCollection(),
        new API\Post()
    ]
)]
#[API\ApiResource(
    uriTemplate: '/photo/locations/{id}',
    uriVariables: [
        'id' => new API\Link(
            fromClass: PhotoLocation::class
        )
    ],
    operations: [
        new API\Get(),
        new API\Put(),
        new API\Delete(),
        new API\Patch()
    ]
)]
class PhotoLocation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $name = null;

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
}
