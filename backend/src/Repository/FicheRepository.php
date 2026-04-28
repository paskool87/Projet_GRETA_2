<?php

namespace App\Repository;

use App\Entity\Alternant;
use App\Entity\Fiche;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fiche>
 */
class FicheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fiche::class);
    }

    //    /**
    //     * @return Fiche[] Returns an array of Fiche objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Fiche
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findOneBySemaine(Alternant $alternant, \DateTimeInterface $debut, \DateTimeInterface $fin): ?Fiche
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.alternant = :alternant')
            ->andWhere('f.date_debut >= :debut')
            ->andWhere('f.date_debut <= :fin')
            ->setParameter('alternant', $alternant)
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // SELECT * FROM app.fiche where alternant_id=162 order by date_debut desc limit 1;

    public function findDerniereFiche(Alternant $alternant): ?Fiche
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.alternant = :alternant')
            ->orderBy('f.date_debut', 'DESC')
            ->setParameter('alternant', $alternant)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}
