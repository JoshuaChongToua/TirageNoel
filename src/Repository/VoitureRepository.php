<?php

namespace App\Repository;

use App\Entity\Voiture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Voiture>
 */
class VoitureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voiture::class);
    }

    //    /**
    //     * @return Voiture[] Returns an array of Voiture objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Voiture
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findWithYearLowerThan(int $annee): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.annee < :annee')
            ->orderBy('v.annee', 'ASC')
            ->setParameter('annee', $annee)
            ->getQuery()
            ->getResult();
    }

    public function findHowMany()  
    {
        return $this->createQueryBuilder('v')
            ->select('COUNT(v.id) as total')
            ->getQuery()
            ->getSingleScalarResult();    
    }

    public function findByCat(int $id)
    {
        return $this->createQueryBuilder('v')
            ->where('v.categorie = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
}
