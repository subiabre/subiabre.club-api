<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Metadata as API;
use App\Repository\PhotoImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PhotoImageRepository::class)]
#[API\ApiResource(
    uriTemplate: '/photo/images',
    operations: [
        new API\GetCollection(),
        new API\Post()
    ]
)]
#[API\ApiResource(
    uriTemplate: '/photo/images/{id}',
    uriVariables: [
        'id' => new API\Link(
            fromClass: PhotoImage::class
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
    filterClass: ExistsFilter::class,
    properties: ['item']
)]
class PhotoImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Url()]
    #[Assert\NotBlank()]
    private ?string $url = null;

    #[ORM\OneToMany(mappedBy: 'image', targetEntity: PhotoPortrait::class, orphanRemoval: true)]
    private Collection $portraits;

    #[ORM\ManyToOne(inversedBy: 'photoImages')]
    private ?PhotoItem $item = null;

    public function __construct()
    {
        $this->portraits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

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
            $portrait->setImage($this);
        }

        return $this;
    }

    public function removePortrait(PhotoPortrait $portrait): self
    {
        if ($this->portraits->removeElement($portrait)) {
            // set the owning side to null (unless already changed)
            if ($portrait->getImage() === $this) {
                $portrait->setImage(null);
            }
        }

        return $this;
    }

    public function getItem(): ?PhotoItem
    {
        return $this->item;
    }

    public function setItem(?PhotoItem $item): self
    {
        $this->item = $item;

        return $this;
    }
}
