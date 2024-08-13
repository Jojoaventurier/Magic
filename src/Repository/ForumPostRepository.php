<?php

namespace App\Repository;

use App\Entity\ForumPost;
use App\Model\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<ForumPost>
 */
class ForumPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumPost::class);
    }

    public function findByTopic($topic): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.forumTopic = :val')
            ->setParameter('val', $topic)
            ->addOrderBy('p.creationDate', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findBySearch(SearchData $searchData): PaginationInterface
    {
        $posts = $this->createQueryBuilder('p')
            ->where('p.state LIKE :state')
            ->setParameter('state', '%STATE_PUBLISHED%')
            ->addOrderBy('p.creationDate', 'DESC');

            if(!empty($searchData->q)) {
                $posts = $posts
                ->andWhere('p.title Like :q')
                ->setParameter('q', "%{$searchData->q}%");
            }

            $posts = $posts
                ->getQuery()
                ->getResult();

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
