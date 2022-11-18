<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\PhotoMedia;
use App\Repository\PhotoRepository;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class PhotoMediaStateProvider implements ProviderInterface
{
    public function __construct(
        private PhotoRepository $photoRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $photo = $this->photoRepository->find($uriVariables['id']);

        if (!$photo) throw new ResourceNotFoundException();

        $photoMedia = new PhotoMedia();
        $photoMedia->setPhoto($photo);

        return $photoMedia;
    }
}
