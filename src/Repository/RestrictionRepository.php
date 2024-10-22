<?php

namespace App\Repository;

use App\Entity\Restriction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Restriction>
 */
class RestrictionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Restriction::class);
    }

    //    /**
    //     * @return Restriction[] Returns an array of Restriction objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Restriction
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByPartieId(int $partieId): array
    {
        return $this->createQueryBuilder('pr') // 'pr' est un alias pour PartieRejoint
        ->andWhere('pr.partie = :partieId') // Condition sur l'ID de la partie
            ->setParameter('partieId', $partieId)
            ->getQuery()
            ->getResult(); // Renvoie un tableau d'objets PartieRejoint
    }

    public function findRestriction(int $idPartie, int $joueurId, int $interditId): ?Restriction
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.partie = :idPartie')
            ->andWhere('r.joueur = :joueurId')
            ->andWhere('r.interdit = :interditId')
            ->setParameter('idPartie', $idPartie)
            ->setParameter('joueurId', $joueurId)
            ->setParameter('interditId', $interditId)
            ->getQuery()
            ->getOneOrNullResult(); // Renvoie une seule restriction ou null
    }

}
