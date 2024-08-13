<?php

namespace App\Repository;

use App\Entity\ForumTopic;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<ForumTopic>
 */
class ForumTopicRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginatorInterface)
    {
        parent::__construct($registry, ForumTopic::class);
    }

    /**
     * Récupérer les posts créés classés par date de création, avec inclusion de la pagination
     * @param int $page
     * @return PaginationInterface
     */

    public function findByLast(int $page): PaginationInterface
    {
        $data = $this->createQueryBuilder('p')
            ->addOrderBy('p.creationDate', 'DESC')
            ->getQuery()
            ->getResult();

            $topics = $this->paginatorInterface->paginate($data, $page, 9);

            return $topics;
    }

    public function findBySubCategory($subCategory): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.forumSubCategory = :val')
            ->setParameter('val', $subCategory)
            ->addOrderBy('p.creationDate', 'DESC')
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
