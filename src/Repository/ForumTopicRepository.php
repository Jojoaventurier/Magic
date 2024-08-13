<?php

namespace App\Repository;

use App\Entity\ForumTopic;
use App\Entity\ForumSubCategory;
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
     * Récupérer les sujets créés classés par date de création, avec inclusion de la pagination
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

     /**
     * Récupérer les sujets créés d'une sous-catégorie,  classés  par date de création, avec inclusion de la pagination
     * @param int $page
     * @param ForumSubCategory $subCategory
     * @return PaginationInterface
     */

    public function findBySubCategory(int $page, ForumSubCategory $subCategory ): PaginationInterface
    {
        $data =  $this->createQueryBuilder('p')
            ->andWhere('p.forumSubCategory = :val')
            ->setParameter('val', $subCategory)
            ->addOrderBy('p.creationDate', 'DESC')
            ->getQuery()
            ->getResult();

            $topics = $this->paginatorInterface->paginate($data, $page, 9);

            return $topics;
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
