<?php

namespace App\Repository;

use App\Entity\ForumPost;
use App\Model\SearchData;
use App\Entity\ForumTopic;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<ForumPost>
 */
class ForumPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginatorInterface)
    {
        parent::__construct($registry, ForumPost::class);
    }


         /**
     * Récupérer les posts d'un sujet, classés  par date de création, avec inclusion de la pagination
     * @param int $page
     * @param ForumTopic $topic
     * @return PaginationInterface
     */
    public function findByTopic(int $page, ForumTopic $topic): PaginationInterface
    {
        $data = $this->createQueryBuilder('p')
            ->andWhere('p.forumTopic = :val')
            ->setParameter('val', $topic)
            ->addOrderBy('p.creationDate', 'ASC')
            ->getQuery()
            ->getResult();

        $posts = $this->paginatorInterface->paginate($data, $page, 9);
            return $posts;
    }


//    /**
//     * @return ForumPost[] Returns an array of ForumPost objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ForumPost
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
