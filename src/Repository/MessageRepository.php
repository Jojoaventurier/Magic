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

    // public function findByMostRecent($author) {

    //     return $this->createQueryBuilder('m')
    //          ->innerJoin('m.author', 'a')
    //         ->innerJoin('m.receiver', 'r')
    //         ->select('a.userName AS authorName, r.userName AS receiverName, a.id AS authorId, r.id AS receiverId, m.isRead AS isRead, MAX(m.createdAt) as lastMessageDate')
    //         ->andWhere('(m.author = :author OR m.receiver = :author)')
    //         ->setParameter('author', $author)
    //         ->setParameter('receiver', $author)
    //         ->groupBy('m.author', 'm.receiver', 'm.isRead')
    //         ->orderBy('lastMessageDate', 'DESC')
    //         ->setMaxResults(10)
    //         ->getQuery()
    //         ->getResult();
    // }

    public function findByMostRecent($currentUser)
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('m.author', 'a')
            ->innerJoin('m.receiver', 'r')
            ->select('a.userName AS authorName, r.userName AS receiverName, a.id AS authorId, r.id AS receiverId, m.isRead AS isRead, MAX(m.createdAt) as lastMessageDate')
            ->andWhere('(m.author = :currentUser OR m.receiver = :currentUser)')
            ->setParameter('currentUser', $currentUser)
            ->groupBy('a.id, r.id, m.isRead') // Include read in group by
            ->orderBy('lastMessageDate', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findByMostRecentUser($currentUser)
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('m.author', 'a')
            ->innerJoin('m.receiver', 'r')
            ->select('a.userName AS authorName, r.userName AS receiverName, a.id AS authorId, r.id AS receiverId, m.isRead AS isRead, MAX(m.createdAt) as lastMessageDate')
            ->andWhere('(m.author = :currentUser OR m.receiver = :currentUser)')
            ->setParameter('currentUser', $currentUser)
            ->groupBy('a.id, r.id, m.isRead') // Include read in group by
            ->orderBy('lastMessageDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countUnreadMessages($currentUser)
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)') // Count the number of message IDs
            ->where('m.receiver = :currentUser') // Condition for the receiver
            ->andWhere('m.isRead = false') // Condition for unread messages
            ->setParameter('currentUser', $currentUser) // Set the parameter
            ->getQuery() // Get the query object
            ->getSingleScalarResult(); // Execute and retrieve the count
    }

    // public function findByMostRecentGroupedByReceiver($currentUser) {

    //     return $this->createQueryBuilder('m')
    //         ->innerJoin('m.author', 'a') // Join the author entity
    //         ->select('a.userName, a.id, MAX(m.createdAt) as lastMessageDate') 
    //         ->andWhere('(m.receiver = :currentUser) OR (m.author = :currentUser)')
    //         ->setParameter('currentUser', $currentUser)
    //         ->groupBy('a.id')
    //         ->orderBy('lastMessageDate', 'DESC')
    //         ->getQuery()
    //         ->getResult();
    // }

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
