<?php

namespace App\Repository;

use App\Entity\PhotoImagePortrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PhotoImagePortrait>
 *
 * @method PhotoImagePortrait|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhotoImagePortrait|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhotoImagePortrait[]    findAll()
 * @method PhotoImagePortrait[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoImagePortraitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhotoImagePortrait::class);
    }

    public function save(PhotoImagePortrait $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PhotoImagePortrait $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PhotoImagePortrait[] Returns an array of PhotoImagePortrait objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PhotoImagePortrait
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
