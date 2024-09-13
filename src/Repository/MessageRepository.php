<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findByMostRecent($author) {

        return $this->createQueryBuilder('m')
            ->andWhere('(m.author = :author AND m.receiver = :receiver) OR (m.author = :receiver AND m.receiver = :author)')
            ->setParameter('author', $author)
            ->setParameter('receiver', $author)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByMostRecentGroupedByReceiver($currentUser) {

        return $this->createQueryBuilder('m')
            ->innerJoin('m.author', 'a') // Join the author entity
            ->select('a.userName, a.id, MAX(m.createdAt) as lastMessageDate') 
            ->andWhere('(m.receiver = :receiver)')
            ->setParameter('receiver', $currentUser)
            ->groupBy('a.id')
            ->orderBy('lastMessageDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByUsers($author, $receiver): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('(m.author = :author AND m.receiver = :receiver) OR (m.author = :receiver AND m.receiver = :author)')
            ->setParameter('author', $author)
            ->setParameter('receiver', $receiver)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
    
    //    /**
    //     * @return Message[] Returns an array of Message objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Message
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
