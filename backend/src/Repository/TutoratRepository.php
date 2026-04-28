<?php

namespace App\Repository;

use App\Entity\Alternant;
use App\Entity\Tutorat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tutorat>
 */
class TutoratRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tutorat::class);
    }

    //    /**
    //     * @return Tutorat[] Returns an array of Tutorat objects
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

    //    public function findOneBySomeField($value): ?Tutorat
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findFichebyTuteurId(int $tuteur_id, int $fiche_id): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT f.id as fiche,a.id as alternant  FROM App\Entity\Fiche f
                LEFT JOIN App\Entity\Alternant  a ON f.alternant=a.id
                LEFT JOIN App\Entity\Tutorat  t ON t.alternant=f.alternant
                where t.tuteur = :tuteur_id AND f.id = :fiche_id'
        )->setParameter('tuteur_id', $tuteur_id)
            ->setParameter('fiche_id', $fiche_id);

        // returns an array of Product objects
        return $query->getResult();
    }

    // SELECT * FROM app.alternant as a left join app.tutorat as t on t.alternant_id=a.id where t.tuteur_id=398;

    public function findAlternantByTuteurId(int $tuteur_id, int $alternant_id): ?Tutorat
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.tuteur = :tuteur')
            ->andWhere('t.alternant = :alternant')
            ->setParameter('tuteur', $tuteur_id)
            ->setParameter('alternant', $alternant_id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
