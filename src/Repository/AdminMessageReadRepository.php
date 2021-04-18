<?php

namespace App\Repository;

use App\Entity\AdminMessageRead;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AdminMessageRead|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminMessageRead|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminMessageRead[]    findAll()
 * @method AdminMessageRead[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminMessageReadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminMessageRead::class);
    }

    // /**
    //  * @return AdminMessageRead[] Returns an array of AdminMessageRead objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AdminMessageRead
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
