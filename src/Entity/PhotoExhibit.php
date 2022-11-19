<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata as API;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\PhotoExhibitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PhotoExhibitRepository::class)]
#[UniqueEntity(fields: ['title'])]
#[API\ApiResource(
    uriTemplate: '/photo/exhibit',
    operations: [
        new API\GetCollection(),
        new API\Post()
    ]
)]
#[API\ApiResource(
    uriTemplate: '/photo/exhibit/{id}',
    uriVariables: [
        'id' => new API\Link(
            fromClass: PhotoExhibit::class
        )
    ],
    operations: [
        new API\Get(),
        new API\Put(),
        new API\Delete(),
        new API\Patch()
    ]
)]
#[ApiFilter(
    filterClass: DateFilter::class,
    properties: [
        'dateMin',
        'dateMax'
    ]
)]
#[ApiFilter(
    filterClass: OrderFilter::class,
    properties: [
        'id',
        'dateMin',
        'dateMax'
    ],
    arguments: [
        'orderParameterName' => 'order'
    ]
)]
class PhotoExhibit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: PhotoImage::class, inversedBy: 'exhibits')]
    private Collection $images;

    #[ORM\ManyToOne]
    private ?PhotoLocation $location = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateMin = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateMax = null;

    public function __construct()
    {
        $this->images = new ArrayCollection();
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
     * @return Collection<int, PhotoImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(PhotoImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
        }

        return $this;
    }

    public function removeImage(PhotoImage $image): self
    {
        $this->images->removeElement($image);

        return $this;
    }

    public function getLocation(): ?PhotoLocation
    {
        return $this->location;
    }

    public function setLocation(?PhotoLocation $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getDateMin(): ?\DateTimeInterface
    {
        return $this->dateMin;
    }

    public function setDateMin(\DateTimeInterface $dateMin): self
    {
        $this->dateMin = $dateMin;

        return $this;
    }

    public function getDateMax(): ?\DateTimeInterface
    {
        return $this->dateMax;
    }

    public function setDateMax(\DateTimeInterface $dateMax): self
    {
        $this->dateMax = $dateMax;

        return $this;
    }
}
