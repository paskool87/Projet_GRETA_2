<?php

namespace App\Repository;

use App\Entity\Alternant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Alternant>
 */
class AlternantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alternant::class);
    }

    //    /**
    //     * @return Alternant[] Returns an array of Alternant objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    public function findAlternantByUtilisateurID(int $idUtilisateur): ?Alternant
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.utilisateur_id = :val')
            ->setParameter('a.utilisateur_id', $idUtilisateur)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    //     /**
    //      * SELECT
    //     a.id AS alternant_id,
    //     a.utilisateur_id as utilisateur_alternant_id ,
    //     u.nom,
    //     u.prenom,
    //     sp.professeur_id AS referent_id,
    //     t.tuteur_id
    // FROM alternant a
    // INNER JOIN utilisateur u ON a.utilisateur_id = u.id
    // INNER JOIN tutorat t ON t.alternant_id = a.id
    // LEFT JOIN suivi_pedagogique sp ON sp.alternant_id = a.id
    // WHERE sp.professeur_id= 387
    //      * ;
    //      */

    public function findAlternantByReferentID(int $idReferent): array
    {
        return $this->createQueryBuilder('a')
            ->select('a', 'u', 'sp', 't')
            ->innerJoin('a.utilisateur', 'u')
            ->innerJoin('a.tutorats', 't')
            ->leftJoin('a.suiviPedagogiques', 'sp')
            ->andWhere('sp.professeur = :idReferent OR t.tuteur = :idReferent')
            ->setParameter('idReferent', $idReferent)
            ->getQuery()
            ->getResult()
        ;
    }
}
