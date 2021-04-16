<?php

namespace App\Repository;

use App\Entity\Announce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Announce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Announce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Announce[]    findAll()
 * @method Announce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnounceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Announce::class);
    }

    public function findCities()
    {
        $qb = $this->createQueryBuilder('a');
        $qb->orderBy('a.city', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function searchAnnounceByWordsCriteria($words = null, $type = null, $city = null, int $priceMin = null, int $priceMax = null, $areaMin = null, $areaMax = null, $roomsMin = null, $roomsMax = null, $bedroomsMin = null, $bedroomsMax = null) {
        $qb = $this->createQueryBuilder('a');
        if($words != null) {
            $qb->where('MATCH_AGAINST(a.title, a.description) AGAINST(:words boolean)>0')
                ->setParameter('words', $words);
        }
        if($type != null) {
            $qb->andWhere('a.type = :type')
                ->setParameter('type', $type);
        }
        if($city != null) {
            $qb->andWhere('a.city = :city')
                ->setParameter('city', $city);
        }
        if($priceMin != null) {
            $qb->andWhere('a.price >= :pricemin')
                ->setParameter('pricemin', $priceMin);
        }
        if($priceMax != null) {
            $qb->andWhere('a.price <= :pricemax')
                ->setParameter('pricemax', $priceMax);
        }
        if($areaMin != null) {
            $qb->andWhere('a.area >= :areamin')
                ->setParameter('areamin', $areaMin);
        }
        if($areaMax != null) {
            $qb->andWhere('a.area <= :areamax')
                ->setParameter('areamax', $areaMax);
        }
        if($roomsMin != null) {
            $qb->andWhere('a.rooms >= :roomsmin')
                ->setParameter('roomsmin', $roomsMin);
        }
        if($roomsMax != null) {
            $qb->andWhere('a.rooms <= :roomsmax')
                ->setParameter('roomsmax', $roomsMax);
        }
        if($bedroomsMin != null) {
            $qb->andWhere('a.bedrooms >= :bedroomsmin')
                ->setParameter('bedroomsmin', $bedroomsMin);
        }
        if($bedroomsMax != null) {
            $qb->andWhere('a.bedrooms <= :bedroomsmax')
                ->setParameter('bedroomsmax', $bedroomsMax);
        }
            // $qb->andWhere('a.active = :val')
            //     ->setParameters('val', true);
        
        return $qb->getQuery()->getResult();
   }

    // /**
    //  * @return Announce[] Returns an array of Announce objects
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
    public function findOneBySomeField($value): ?Announce
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
