<?php

namespace App\Repository;

use App\Entity\TirageResultat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TirageResultat>
 */
class TirageResultatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TirageResultat::class);
    }

    //    /**
    //     * @return TirageResultat[] Returns an array of TirageResultat objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?TirageResultat
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByPartieId($id): ?array
    {
        return $this->createQueryBuilder('tr')
        ->select('j.id as joueurId, d.id as destinataireId, p.id as partieId')  // Sélection des IDs du joueur, destinataire et partie
        ->join('tr.joueur', 'j')  // Jointure avec User pour le joueur
        ->join('tr.destinataire', 'd')  // Jointure avec User pour le destinataire
        ->join('tr.partie', 'p')  // Jointure avec Partie
        ->where('p.id = :id')  // Filtrer par l'ID de la partie
        ->setParameter('id', $id)
        ->getQuery()
        ->getScalarResult();
    }

}
