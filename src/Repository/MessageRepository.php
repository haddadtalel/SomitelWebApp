<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Ticket;
use App\Entity\User;
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

    /**
     * Find messages by ticket
     */
    public function findByTicket(Ticket $ticket, array $orderBy = ['createdAt' => 'ASC']): array
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.ticket = :ticket')
            ->setParameter('ticket', $ticket);

        foreach ($orderBy as $field => $direction) {
            $qb->orderBy("m.$field", $direction);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Find unread messages for a user
     */
    public function findUnreadByUser(User $user): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.recipient = :user')
            ->andWhere('m.isRead = :isRead')
            ->setParameter('user', $user)
            ->setParameter('isRead', false)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find unread messages for admin (messages with null recipient)
     */
    public function findUnreadForAdmin(): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.recipient IS NULL')
            ->andWhere('m.isRead = :isRead')
            ->setParameter('isRead', false)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Count unread messages for a user
     */
    public function countUnreadByUser(User $user): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->andWhere('m.recipient = :user')
            ->andWhere('m.isRead = :isRead')
            ->setParameter('user', $user)
            ->setParameter('isRead', false)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Count unread messages for admin
     */
    public function countUnreadForAdmin(): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->andWhere('m.recipient IS NULL')
            ->andWhere('m.isRead = :isRead')
            ->setParameter('isRead', false)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Mark all messages in a ticket as read for a user
     */
    public function markAllAsReadInTicket(Ticket $ticket, User $user): void
    {
        $qb = $this->createQueryBuilder('m')
            ->update()
            ->set('m.isRead', ':isRead')
            ->set('m.readAt', ':readAt')
            ->andWhere('m.ticket = :ticket')
            ->andWhere('m.recipient = :user')
            ->andWhere('m.isRead = :notRead')
            ->setParameter('isRead', true)
            ->setParameter('readAt', new \DateTime())
            ->setParameter('ticket', $ticket)
            ->setParameter('user', $user)
            ->setParameter('notRead', false);

        $qb->getQuery()->execute();
    }

    /**
     * Mark all messages in a ticket as read for admin
     */
    public function markAllAsReadInTicketForAdmin(Ticket $ticket): void
    {
        $qb = $this->createQueryBuilder('m')
            ->update()
            ->set('m.isRead', ':isRead')
            ->set('m.readAt', ':readAt')
            ->andWhere('m.ticket = :ticket')
            ->andWhere('m.recipient IS NULL')
            ->andWhere('m.isRead = :notRead')
            ->setParameter('isRead', true)
            ->setParameter('readAt', new \DateTime())
            ->setParameter('ticket', $ticket)
            ->setParameter('notRead', false);

        $qb->getQuery()->execute();
    }
}
