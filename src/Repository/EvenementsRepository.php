<?php

namespace App\Repository;

use App\Entity\Evenements;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evenements>
 */
class EvenementsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenements::class);
    }

    public function getLastEvenements($number){
        $query = $this->createQueryBuilder('e');
        $query->where('e.est_valide = true');
        $query->orderBy('e.id', 'DESC');
        $query->setMaxResults($number);

        return $query->getQuery()->getResult();
    }

    public function getNumberOfEvenements(){
        $query = $this->createQueryBuilder('e');
        $query->select('count(e.id)');
        $query->where('e.est_valide = true');

        $event = $query->getQuery()->getSingleScalarResult();
        return $event;
    }

    //    /**
    //     * @return Evenements[] Returns an array of Evenements objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Evenements
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
