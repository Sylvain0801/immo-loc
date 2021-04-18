<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findMessagesByRecipientRole($user, string $role, string $header, string $sorting)
    {
        $qb = $this->createQueryBuilder('m');
        $qb->leftJoin('m.recipient', 'r')
            ->where('r.roles LIKE :roles')
            ->setParameter('roles', '%"'.$role.'"%')
            ->orderBy('m.'.$header, $sorting);

        return $qb->getQuery()->getResult();
    }

    public function findMessagesByAdmin($user, string $header, string $sorting)
    {
        $qb = $this->createQueryBuilder('m');
        if($header !== 'messageReads') {
            $qb->leftJoin('m.admin_recipient', 'r')
                ->where('r.id = :id')
                ->setParameter('id', $user->getId())
                ->orderBy('m.'.$header, $sorting);
        }
        if($header === 'messageReads') {
            $qb->leftJoin('m.admin_recipient', 'r')
                ->where('r.id = :id')
                ->setParameter('id', $user->getId())
                ->leftJoin('m.adminMessageReads', 'mr')
                ->addSelect('mr')
                ->andWhere('mr.admin = :admin')
                ->setParameter('admin', $user)
                ->orderBy('mr.not_read', $sorting);
        } 

        return $qb->getQuery()->getResult();
    }

    public function findMessagesByUser($user, string $header, string $sorting)
    {
        $qb = $this->createQueryBuilder('m');
        if($header !== 'messageReads') {
            $qb->leftJoin('m.recipient', 'r')
                ->where('r.id = :id')
                ->setParameter('id', $user->getId())
                ->orderBy('m.'.$header, $sorting);
        }
        if($header === 'messageReads') {
            $qb->leftJoin('m.recipient', 'r')
                ->where('r.id = :id')
                ->setParameter('id', $user->getId())
                ->leftJoin('m.messageReads', 'mr')
                ->addSelect('mr')
                ->andWhere('mr.user = :user')
                ->setParameter('user', $user)
                ->orderBy('mr.not_read', $sorting);
        } 

        return $qb->getQuery()->getResult();
    }

    
    // /**
    //  * @return Message[] Returns an array of Message objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Message
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
