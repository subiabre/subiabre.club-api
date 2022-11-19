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
    uriTemplate: '/photo/image',
    operations: [
        new API\GetCollection(),
        new API\Post()
    ]
)]
#[API\ApiResource(
    uriTemplate: '/photo/image/{id}',
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
    properties: ['exhibits']
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

    #[ORM\OneToMany(mappedBy: 'image', targetEntity: PhotoImagePortrait::class, orphanRemoval: true)]
    private Collection $portraits;

    #[ORM\ManyToMany(targetEntity: PhotoExhibit::class, mappedBy: 'images')]
    private Collection $exhibits;

    public function __construct()
    {
        $this->portraits = new ArrayCollection();
        $this->exhibits = new ArrayCollection();
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
     * @return Collection<int, PhotoImagePortrait>
     */
    public function getPortraits(): Collection
    {
        return $this->portraits;
    }

    public function addPortrait(PhotoImagePortrait $portrait): self
    {
        if (!$this->portraits->contains($portrait)) {
            $this->portraits->add($portrait);
            $portrait->setImage($this);
        }

        return $this;
    }

    public function removePortrait(PhotoImagePortrait $portrait): self
    {
        if ($this->portraits->removeElement($portrait)) {
            // set the owning side to null (unless already changed)
            if ($portrait->getImage() === $this) {
                $portrait->setImage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PhotoExhibit>
     */
    public function getExhibits(): Collection
    {
        return $this->exhibits;
    }

    public function addExhibit(PhotoExhibit $exhibit): self
    {
        if (!$this->exhibits->contains($exhibit)) {
            $this->exhibits->add($exhibit);
            $exhibit->addImage($this);
        }

        return $this;
    }

    public function removeExhibit(PhotoExhibit $exhibit): self
    {
        if ($this->exhibits->removeElement($exhibit)) {
            $exhibit->removeImage($this);
        }

        return $this;
    }
}
