<?php

namespace App\Repository;

use App\Entity\PublicUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PublicUser>
 */
class PublicUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublicUser::class);
    }

    public function countByMonth(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT YEAR(created_at) as annee, MONTH(created_at) as mois, COUNT(id) as total
            FROM public_user
            WHERE created_at IS NOT NULL
            GROUP BY annee, mois
            ORDER BY annee, mois ASC';
        return $conn->executeQuery($sql)->fetchAllAssociative();
    }

    //    /**
    //     * @return PublicUser[] Returns an array of PublicUser objects
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

    //    public function findOneBySomeField($value): ?PublicUser
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
