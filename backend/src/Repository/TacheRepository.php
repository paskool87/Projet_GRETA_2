<?php

namespace App\Repository;

use App\Entity\Tache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tache>
 */
class TacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tache::class);
    }

    //    /**
    //     * @return Tache[] Returns an array of Tache objects
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

    //    public function findOneBySomeField($value): ?Tache
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Trouve une tache a la date indique
     * @param \DateTimeInterface $date date de la recherche
     * @param int $fiche_id Fiche 
     */
    public function findOneTachebyDate(\DateTimeInterface $date, int $fiche_id): ?Tache
    {
        return $this->createQueryBuilder("t")
            ->andWhere("t.fiche = :fiche_id")
            ->andWhere("t.date_tache = :date_tache")
            ->setParameter("fiche_id", $fiche_id)
            ->setParameter("date_tache", $date)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
