<?php

namespace App\Repository;

use App\Entity\ForumTopic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ForumTopic>
 */
class ForumTopicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumTopic::class);
    }


    public function findBySubCategory($subCategory): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.forumSubCategory = :val')
            ->setParameter('val', $subCategory)
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
    //     * @return ForumTopic[] Returns an array of ForumTopic objects
    //     */
    //    public function findById($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.id = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ForumTopic
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
