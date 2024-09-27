<?php

namespace App\Repository;

use App\Entity\PartieRejoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PartieRejoint>
 */
class PartieRejointRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PartieRejoint::class);
    }

    //    /**
    //     * @return PartieRejoint[] Returns an array of PartieRejoint objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PartieRejoint
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByUserId($id): ?array
    {
        return $this->createQueryBuilder('pr')
            ->select('pr.role as role, pr.id as partieRejointId, p.id as partieId, u.id as userId')  // Sélectionne les IDs de PartieRejoint, Partie et User
            ->join('pr.partie', 'p')  // Jointure entre PartieRejoint et Partie
            ->join('pr.user', 'u')    // Jointure entre PartieRejoint et User
            ->where('pr.user = :id')  // Condition sur l'utilisateur
            ->setParameter('id', $id)
            ->getQuery()
            ->getScalarResult();
    }

    public function findByPartieId($id): ?array
    {
        return $this->createQueryBuilder('pr')
            ->select('pr.souhaits as souhaits, pr.role as role, pr.id as partieRejointId, p.id as partieId, u.id as userId')  // Sélectionne les IDs de PartieRejoint, Partie et User
            ->join('pr.partie', 'p')  // Jointure entre PartieRejoint et Partie
            ->join('pr.user', 'u')    // Jointure entre PartieRejoint et User
            ->where('pr.partie = :id')  // Condition sur la partie
            ->setParameter('id', $id)
            ->getQuery()
            ->getScalarResult();
    }
}
