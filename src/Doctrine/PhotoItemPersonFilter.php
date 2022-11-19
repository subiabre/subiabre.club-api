<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

final class PhotoItemPersonFilter extends AbstractFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        // otherwise filter is applied to order and page as well
        if (
            !$this->isPropertyEnabled($property, $resourceClass)
        ) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->innerJoin(sprintf('%s.images', $alias), 'pi')
            ->innerJoin(sprintf('%s.portraits', 'pi'), 'ps')
            ->innerJoin(sprintf('%s.person', 'ps'), 'pp')
            ->where('pp IN (:people)')
            ->setParameter('people', explode(',', $value));
    }

    // This function is only used to hook in documentation generators (supported by Swagger and Hydra)
    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }

        $description = [];
        foreach ($this->properties as $property => $strategy) {
            $description[$property] = [
                'property' => $property,
                'type' => Type::BUILTIN_TYPE_ARRAY,
                'required' => false,
                'openapi' => [
                    'allowReserved' => false,// if true, query parameters will be not percent-encoded
                    'allowEmptyValue' => true,
                    'explode' => true, // to be true, the type must be Type::BUILTIN_TYPE_ARRAY, ?product=blue,green will be ?product=blue&product=green
                ],
            ];
        }

        return $description;
    }
}