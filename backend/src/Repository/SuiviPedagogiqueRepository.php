<?php

namespace App\Repository;

use App\Entity\Alternant;
use App\Entity\SuiviPedagogique;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SuiviPedagogique>
 */
class SuiviPedagogiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuiviPedagogique::class);
    }

    //    /**
    //     * @return SuiviPedagogique[] Returns an array of SuiviPedagogique objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?SuiviPedagogique
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findFichebyProfesseurId(int $professeur_id, int $fiche_id): array
    {
        $entityManager = $this->getEntityManager();


        $query = $entityManager->createQuery(
            'SELECT f.id as fiche,a.id as alternant  FROM App\Entity\Fiche f
                LEFT JOIN App\Entity\Alternant  a ON f.alternant=a.id
                LEFT JOIN App\Entity\SuiviPedagogique  sp ON sp.alternant=f.alternant
                where  sp.professeur = :tuteur_id AND f.id = :fiche_id'
        )->setParameter('tuteur_id', $professeur_id)
            ->setParameter('fiche_id', $fiche_id);


        // returns an array of Product objects
        return $query->getResult();
    }

    public function findAlternantByProfesseurId(int $professeur_id, int $alternant_id): ?SuiviPedagogique
    {
        return $this->createQueryBuilder('sv')
            ->andWhere('sv.professeur = :professeur')
            ->andWhere('sv.alternant = :alternant')
            ->setParameter('professeur', $professeur_id)
            ->setParameter('alternant', $alternant_id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
