<?php

namespace App\Repository;

use App\Entity\Choix;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Choix>
 */
class ChoixRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Choix::class);
    }

    //    /**
    //     * @return Choix[] Returns an array of Choix objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Choix
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByPartieId($id): ?array
    {
        return $this->createQueryBuilder('c')
        ->select('j.id as joueurId, d.id as destinataireId, p.id as partieId')  // Sélection des IDs du joueur, destinataire et partie
        ->join('c.joueur', 'j')  // Jointure avec User pour le joueur
        ->join('c.personneChoisie', 'd')  // Jointure avec User pour le destinataire
        ->join('c.partie', 'p')  // Jointure avec Partie
        ->where('p.id = :id')  // Filtrer par l'ID de la partie
        ->setParameter('id', $id)
        ->getQuery()
        ->getScalarResult();
    }
}
