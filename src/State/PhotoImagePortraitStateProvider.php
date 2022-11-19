<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\PhotoImagePortrait;
use App\Repository\PhotoImageRepository;

class PhotoImagePortraitStateProvider implements ProviderInterface
{
    public function __construct(
        private PhotoImageRepository $photoImageRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $image = $this->photoImageRepository->find($uriVariables['imageId']);

        if (!$image) return null;

        $portrait = new PhotoImagePortrait();
        $portrait->setImage($image);

        return $portrait;
    }
}
