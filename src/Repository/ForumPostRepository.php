<?php

namespace App\Repository;

use App\Entity\ForumPost;
use App\Model\SearchData;
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


    /**
     * Récupère les posts où on retrouve le mot clé saisi par l'utilisateur
     * 
     * @param SearchData $searchData
     * @return PaginationInterface
     */
    public function findBySearch(SearchData $searchData): PaginationInterface
    {
        $data = $this->createQueryBuilder('p')
            ->where('p.state LIKE :state')
            ->setParameter('state', '%STATE_PUBLISHED%')
            ->addOrderBy('p.creationDate', 'DESC');

            if(!empty($searchData->q)) {
                $data = $data
                ->andWhere('p.title Like :q')
                ->setParameter('q', "%{$searchData->q}%");
            }

            $data = $data
                ->getQuery()
                ->getResult();

            $posts = $this->paginatorInterface->paginate($data, $searchData->page, 9);

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
