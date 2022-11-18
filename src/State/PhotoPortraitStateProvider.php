<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\PhotoPortrait;
use App\Repository\PhotoMediaRepository;
use App\Repository\PhotoRepository;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class PhotoPortraitStateProvider implements ProviderInterface
{
    public function __construct(
        private PhotoRepository $photoRepository,
        private PhotoMediaRepository $photoMediaRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $photo = $this->photoRepository->find($uriVariables['id']);
        $photoMedia = $this->photoMediaRepository->find($uriVariables['mediaId']);

        if (!$photo || !$photoMedia) throw new ResourceNotFoundException();

        $photoPortrait = new PhotoPortrait();
        $photoPortrait->setPhoto($photo);
        $photoPortrait->setPhotoMedia($photoMedia);

        return $photoPortrait;
    }
}
